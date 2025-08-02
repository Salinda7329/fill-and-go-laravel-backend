<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Kreait\Firebase\Factory;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class StationOwnerRegistrationController extends Controller
{
    public function showRegistrationForm()
    {
        return view('stationowner.registerstationowner');
    }

    public function register(Request $request)
    {
        try {
            $validated = $request->validate([
                'station_name' => 'required|string|max:255',
                'station_address' => 'required|string|max:255',
                'contact_number' => 'required|string|max:32',
                'email' => 'required|email|unique:mongodb.users,email',
                'password' => 'required|string|min:8|confirmed',
            ]);

            // Create Firebase user if you use Firebase
            $auth = (new Factory)->withServiceAccount(config('services.firebase.credentials'))->createAuth();
            $firebaseUser = $auth->createUser([
                'email' => $validated['email'],
                'password' => $validated['password'],
            ]);

            // Save to MongoDB
            $user = User::create([
                'email' => $validated['email'],
                'password' => bcrypt($validated['password']),
                'firebase_uid' => $firebaseUser->uid,
                'role' => 2, // 2 for station owner
                'status' => 0, // 0 = pending (not verified)
                'station_name' => $validated['station_name'],
                'station_address' => $validated['station_address'],
                'contact_number' => $validated['contact_number'],
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            Log::info('Station owner registered. Awaiting admin approval.', [
                'user_id' => $user->_id,
                'email' => $user->email
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Registered successfully! Your account is pending admin approval.',
                'user_id' => $user->_id,
            ], 201);
        } catch (ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Registration failed: ' . $e->getMessage(),
            ], 500);
        }
    }
}
