<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Kreait\Firebase\Factory;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use App\Mail\WelcomeCustomerMail;


class CustomerRegistrationController extends Controller
{
    public function showRegistrationForm()
    {
        return view('customerregistrationform');
    }

    public function register(Request $request)
    {
        try {
            $validated = $request->validate([
                'email' => 'required|email|unique:mongodb.users,email',
                'password' => 'required|string|min:8|confirmed',
            ]);

            // Create Firebase user
            $auth = (new Factory)->withServiceAccount(config('services.firebase.credentials'))->createAuth();
            $firebaseUser = $auth->createUser([
                'email' => $validated['email'],
                'password' => $validated['password'],
            ]);

            // Create MongoDB user
            $user = User::create([
                'email' => $validated['email'],
                'password' => bcrypt($validated['password']),
                'firebase_uid' => $firebaseUser->uid,
                'role' => 3,
                'status' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            Log::info('User registered successfully', [
                'user_id' => $user->_id,
                'email' => $user->email,
                'firebase_uid' => $firebaseUser->uid,
            ]);

            // Send welcome email
            try {
                Mail::to($user->email)->send(new WelcomeCustomerMail($user));
                Log::info('Welcome email sent to user', [
                    'user_id' => $user->_id,
                    'email' => $user->email,
                ]);
            } catch (\Exception $e) {
                Log::error('Failed to send welcome email', [
                    'user_id' => $user->_id,
                    'email' => $user->email,
                    'error' => $e->getMessage()
                ]);
            }

            return response()->json([
                'success' => true,
                'message' => 'Registered successfully',
                'user_id' => $user->_id,
            ], 201);
        } catch (ValidationException $e) {
            Log::warning('Validation failed during registration:', [
                'errors' => $e->errors(),
                'input' => $request->all(),
            ]);
            return response()->json([
                'success' => false,
                'message' => $e->errors()['email'][0] ?? 'Validation failed',
            ], 422);
        } catch (\Kreait\Firebase\Exception\AuthException $e) {
            Log::error('Firebase registration failed:', [
                'error' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
            ]);
            return response()->json([
                'success' => false,
                'message' => 'Firebase registration failed: ' . $e->getMessage(),
            ], 500);
        } catch (\Illuminate\Database\QueryException $e) {
            if (isset($user)) {
                $user->delete();
            }
            Log::error('MongoDB registration failed:', [
                'error' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
            ]);
            return response()->json([
                'success' => false,
                'message' => 'Database error: ' . $e->getMessage(),
            ], 500);
        } catch (\Throwable $e) {
            if (isset($user)) {
                $user->delete();
            }
            Log::error('Registration failed:', [
                'error' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
            ]);
            return response()->json([
                'success' => false,
                'message' => 'Registration failed: ' . ($e->getMessage() ?: 'Unknown error'),
            ], 500);
        }
    }

    public function createSession(Request $request)
    {
        try {
            $request->validate([
                'id_token' => 'required|string',
            ]);

            $path = config('services.firebase.credentials');
            Log::info('Credentials path: ' . $path, ['exists' => file_exists($path)]);

            if (!file_exists($path)) {
                Log::error('Firebase credentials file not found', ['path' => $path]);
                return response()->json([
                    'success' => false,
                    'message' => 'Server configuration error. Please contact support.'
                ], 500);
            }

            $auth = (new Factory)->withServiceAccount(config('services.firebase.credentials'))->createAuth();
            $verifiedIdToken = $auth->verifyIdToken($request->id_token);
            $uid = $verifiedIdToken->claims()->get('sub');
            $firebaseUser = $auth->getUser($uid);

            $user = User::where(function ($query) use ($uid, $firebaseUser) {
                $query->where('firebase_uid', $uid)
                    ->orWhere(function ($q) use ($firebaseUser) {
                        $q->whereNull('firebase_uid')
                            ->where('email', $firebaseUser->email);
                    });
            })->where('status', 1)->first();

            if (!$user) {
                Log::warning('User not found or inactive', [
                    'uid' => $uid,
                    'email' => $firebaseUser->email
                ]);
                return response()->json([
                    'success' => false,
                    'message' => 'User not registered in the system or account is inactive. Please contact support.'
                ], 403);
            }

            if (!isset($user->role)) {
                Log::warning('User role missing', ['user_id' => $user->_id]);
                return response()->json([
                    'success' => false,
                    'message' => 'User does not have a role assigned. Please contact support.'
                ], 403);
            }

            if (!$user->firebase_uid) {
                $user->firebase_uid = $uid;
                $user->save();
                Log::info('Updated user with firebase_uid', ['user_id' => $user->_id, 'firebase_uid' => $uid]);
            }

            session([
                'user_id' => $user->_id,
                'user_email' => $user->email,
                'user_role' => $user->role,
            ]);

            Log::info('Session set for user:', [
                'user_id' => $user->_id,
                'user_email' => $user->email,
                'user_role' => $user->role,
                'session_data' => session()->all()
            ]);

            Auth::login($user);

            $expiresIn = 60 * 60 * 24 * 5; // 5 days
            $sessionCookie = $auth->createSessionCookie($request->id_token, $expiresIn);

            return response()->json([
                'success' => true,
                'message' => 'Login successful',
                'redirect_url' => route('dashboard')
            ])->cookie('firebase_session', $sessionCookie, $expiresIn / 60, '/', null, false, false, false, 'Lax');
        } catch (ValidationException $e) {
            Log::warning('Validation failed during session creation:', [
                'errors' => $e->errors(),
                'input' => $request->all(),
            ]);
            return response()->json([
                'success' => false,
                'message' => $e->errors()['id_token'][0] ?? 'Validation failed',
            ], 422);
        } catch (\Throwable $e) {
            Log::error('Login failed:', [
                'error' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
            ]);
            return response()->json([
                'success' => false,
                'message' => 'Login failed: ' . ($e->getMessage() ?: 'Unknown error'),
            ], 500);
        }
    }
}
