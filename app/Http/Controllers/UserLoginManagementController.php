<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

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
}
