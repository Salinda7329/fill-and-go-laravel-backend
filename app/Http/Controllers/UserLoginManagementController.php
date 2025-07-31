<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use MongoDB\Laravel\Eloquent\Casts\ObjectId;


class UserLoginManagementController extends Controller
{
    public function dashboard(Request $request)
    {
        if (!Auth::check()) {
            return redirect()->route('login')->with('error', 'Please log in to access the dashboard.');
        }

        $user = Auth::user();

        // Verify status == 1 (optional, as login already enforces this)
        if ($user->status != 1) {
            Auth::logout();
            session()->flush();
            return redirect()->route('login')->with('error', 'Your account is inactive. Please contact support.');
        }

        // Redirect based on role
        switch ($user->role) {
            case 1:
                return redirect()->route('admin.dashboard');
            case 2:
                return redirect()->route('stationowner.dashboard');
            case 3:
                return redirect()->route('customer.dashboard');
            default:
                return redirect()->route('login')->with('error', 'Invalid user role.');
        }
    }

    public function logout(Request $request)
    {
        Log::info('User logging out', [
            'user_id' => Auth::id(),
            'firebase_uid' => $request->firebase_uid,
            'session_data' => session()->all(),
        ]);

        // Log out from Laravel Auth
        Auth::logout();

        // Clear all session data
        session()->flush();

        // Clear the firebase_session cookie
        $cookie = cookie()->forget('firebase_session');

        Log::info('User logged out, session and cookie cleared');

        // Redirect to the home route
        return redirect('/')->with('message', 'Successfully logged out')->withCookie($cookie);
    }
}
