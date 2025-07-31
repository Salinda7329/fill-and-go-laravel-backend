<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Kreait\Firebase\Factory;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;

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
}
