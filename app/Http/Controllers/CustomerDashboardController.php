<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Account;
use App\Models\Topup;
use Illuminate\Support\Facades\Auth;

class CustomerDashboardController extends Controller
{
    // For loading the dashboard view
    public function index()
    {
        return view('customer.customerdashboard');
    }

    // For fetching dashboard stats via AJAX
    public function stats()
    {
        $user = Auth::user();

        $account = Account::where('user_id', $user->_id)->first();
        $balance = $account ? $account->balance : 0;

        $topupCount = Topup::where('user_id', $user->_id)->count();

        return response()->json([
            'balance' => $balance,
            'topupCount' => $topupCount,
        ]);
    }
}
