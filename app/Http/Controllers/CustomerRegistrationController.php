<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Kreait\Firebase\Factory;
use Illuminate\Support\Facades\Cookie;


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

            return response()->json(['message' => 'Registered successfully'], 200);
        } catch (\Throwable $e) {
            if (isset($user)) {
                $user->delete();
            }
            return response()->json(['message' => 'Firebase Error: ' . $e->getMessage()], 500);
        }
    }



    public function createSession(Request $request)
    {
        $request->validate([
            'idToken' => 'required|string',
        ]);

        $auth = (new Factory)->withServiceAccount(config('services.firebase.credentials'))->createAuth();

        try {
            $expiresIn = 60 * 60 * 24 * 5; // 5 days
            $sessionCookie = $auth->createSessionCookie($request->idToken, $expiresIn);

            Cookie::queue(Cookie::make('firebase_session', $sessionCookie, $expiresIn / 60, null, null, true, true, false, 'Strict'));

            return response()->json(['status' => 'session_created']);
        } catch (\Throwable $e) {
            return response()->json(['error' => 'Invalid ID token: ' . $e->getMessage()], 401);
        }
    }
}
