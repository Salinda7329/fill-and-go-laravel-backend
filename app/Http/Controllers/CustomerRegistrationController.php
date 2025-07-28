<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Kreait\Firebase\Factory;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class CustomerRegistrationController extends Controller
{
    public function register(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required|string|min:8|confirmed',
        ]);

        if (User::where('email', $request->email)->exists()) {
            return response()->json(['message' => 'Email already exists'], 422);
        }

        try {
            $user = User::create([
                'email' => $request->email,
                'password' => bcrypt($request->password),
                'role' => 3,
                'status' => 1,
            ]);

            $auth = (new Factory)->withServiceAccount(config('services.firebase.credentials'))->createAuth();
            $firebaseUser = $auth->createUser([
                'email' => $request->email,
                'password' => $request->password,
            ]);

            $user->firebase_uid = $firebaseUser->uid;
            $user->save();

            Log::info('User registered successfully', [
                'user_id' => $user->_id,
                'email' => $user->email,
                'firebase_uid' => $firebaseUser->uid
            ]);

            return response()->json(['message' => 'Registered successfully'], 200);
        } catch (\Throwable $e) {
            if (isset($user)) {
                $user->delete();
            }
            Log::error('Registration failed:', [
                'error' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine()
            ]);
            return response()->json(['message' => 'Firebase Error: ' . $e->getMessage()], 500);
        }
    }

    // public function createSession(Request $request)
    // {
    //     $request->validate([
    //         'idToken' => 'required|string',
    //     ]);

    //     $path = config('services.firebase.credentials');
    //     Log::info('Credentials path: ' . $path, ['exists' => file_exists($path)]);

    //     if (!file_exists($path)) {
    //         Log::error('Firebase credentials file not found', ['path' => $path]);
    //         return response()->json([
    //             'error' => "Firebase credentials file not found at {$path}",
    //             'message' => 'Server configuration error. Please contact support.'
    //         ], 500);
    //     }

    //     try {
    //         $auth = (new Factory)->withServiceAccount(config('services.firebase.credentials'))->createAuth();
    //         $verifiedIdToken = $auth->verifyIdToken($request->idToken);
    //         $uid = $verifiedIdToken->claims()->get('sub');
    //         $firebaseUser = $auth->getUser($uid);

    //         $user = User::where(function ($query) use ($uid, $firebaseUser) {
    //             $query->where('firebase_uid', $uid)
    //                 ->orWhere(function ($q) use ($firebaseUser) {
    //                     $q->whereNull('firebase_uid')
    //                         ->where('email', $firebaseUser->email);
    //                 });
    //         })->where('status', 1)->first();

    //         if (!$user) {
    //             Log::warning('User not found or inactive', [
    //                 'uid' => $uid,
    //                 'email' => $firebaseUser->email
    //             ]);
    //             return response()->json([
    //                 'message' => 'User not registered in the system or account is inactive. Please contact support.'
    //             ], 403);
    //         }

    //         if (!isset($user->role)) {
    //             Log::warning('User role missing', ['user_id' => $user->_id]);
    //             return response()->json([
    //                 'message' => 'User does not have a role assigned. Please contact support.'
    //             ], 403);
    //         }

    //         if (!$user->firebase_uid) {
    //             $user->firebase_uid = $uid;
    //             $user->save();
    //             Log::info('Updated user with firebase_uid', ['user_id' => $user->_id, 'firebase_uid' => $uid]);
    //         }

    //         session([
    //             'user_id' => $user->_id,
    //             'user_email' => $user->email,
    //             'user_role' => $user->role,
    //         ]);

    //         Log::info('Session set for user:', [
    //             'user_id' => $user->_id,
    //             'user_email' => $user->email,
    //             'user_role' => $user->role,
    //             'session_data' => session()->all()
    //         ]);

    //         Auth::login($user);

    //         $expiresIn = 60 * 60 * 24 * 5; // 5 days
    //         $sessionCookie = $auth->createSessionCookie($request->idToken, $expiresIn);

    //         return response()->json([
    //             'message' => 'Login successful',
    //             'redirect_url' => route('dashboard')
    //         ])->cookie('firebase_session', $sessionCookie, $expiresIn / 60, null, null, true, true, false, 'Strict');
    //     } catch (\Throwable $e) {
    //         Log::error('Login failed:', [
    //             'error' => $e->getMessage(),
    //             'file' => $e->getFile(),
    //             'line' => $e->getLine()
    //         ]);
    //         return response()->json([
    //             'message' => 'Login failed due to a server error. Please try again or contact support.',
    //             'error' => $e->getMessage()
    //         ], 500);
    //     }
    // }

    public function createSession(Request $request)
    {
        $request->validate([
            'idToken' => 'required|string',
        ]);

        $path = config('services.firebase.credentials');
        Log::info('Credentials path: ' . $path, ['exists' => file_exists($path)]);

        if (!file_exists($path)) {
            Log::error('Firebase credentials file not found', ['path' => $path]);
            return response()->json([
                'error' => "Firebase credentials file not found at {$path}",
                'message' => 'Server configuration error. Please contact support.'
            ], 500);
        }

        try {
            $auth = (new Factory)->withServiceAccount(config('services.firebase.credentials'))->createAuth();
            $verifiedIdToken = $auth->verifyIdToken($request->idToken);
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
                    'message' => 'User not registered in the system or account is inactive. Please contact support.'
                ], 403);
            }

            if (!isset($user->role)) {
                Log::warning('User role missing', ['user_id' => $user->_id]);
                return response()->json([
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
            $sessionCookie = $auth->createSessionCookie($request->idToken, $expiresIn);

            return response()->json([
                'message' => 'Login successful',
                'redirect_url' => route('dashboard')
            ])->cookie('firebase_session', $sessionCookie, $expiresIn / 60, '/', null, false, false, false, 'Lax');
        } catch (\Throwable $e) {
            Log::error('Login failed:', [
                'error' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine()
            ]);
            return response()->json([
                'message' => 'Login failed due to a server error. Please try again or contact support.',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
