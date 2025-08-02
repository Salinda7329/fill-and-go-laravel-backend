<?php

namespace App\Http\Controllers;

use App\Models\Topup;
use Illuminate\Support\Facades\Auth;


class StationOwnerTransactionController extends Controller
{
    public function index()
    {
         $user = Auth::user();
        // Assuming each owner has a station_id field or relation
        $stationId = $user->station_id;
        $transactions = Topup::where('station_id', $stationId)->orderBy('created_at', 'desc')->get();
        return view('stationowner.stationownermanagetransactions', compact('transactions'));
    }
}
