<?php

namespace App\Http\Controllers;

use App\Models\Vehicle;
use App\Models\Account;
use Illuminate\Http\Request;
use MongoDB\Laravel\Eloquent\Casts\ObjectId;
use Illuminate\Support\Facades\Auth;

class TopupController extends Controller
{
    public function showTopupForm(Request $request)
    {
        $user = Auth::user();
        if (!$user) {
            abort(403, 'Unauthorized');
        }

        $vehicles = Vehicle::where('user_id', $user->_id)->get();
        $accounts = Account::where('user_id', $user->_id)->get();

        return view('topup', compact('vehicles', 'accounts'));
    }

    public function storeTopup(Request $request)
    {
        $validated = $request->validate([
            'account_number' => 'required|exists:accounts,_id', // Use _id as account_number
            'vehicle_id' => 'required|exists:vehicles,_id',
            'amount' => 'required|numeric|min:1',
            'image' => 'required|file|mimes:jpg,jpeg,png,gif|max:2048',
        ]);

        $user = Auth::user();
        if (!$user) {
            return response()->json(['message' => 'User not found.'], 404);
        }

        // Verify vehicle belongs to user
        $vehicle = Vehicle::where('_id', $validated['vehicle_id'])
            ->where('user_id', $user->_id)
            ->first();
        if (!$vehicle) {
            return response()->json(['message' => 'Invalid vehicle selected.'], 422);
        }

        // Verify account belongs to user
        $account = Account::where('_id', $validated['account_number'])
            ->where('user_id', $user->_id)
            ->first();
        if (!$account) {
            return response()->json(['message' => 'Invalid account selected.'], 422);
        }

        // Store payment proof
        $filePath = $request->file('image')->store('public/proofs');

        // Create top-up record
        $topup = new Topup();
        $topup->_id = new ObjectId();
        $topup->user_id = $user->_id;
        $topup->vehicle_id = $vehicle->_id;
        $topup->account_number = $validated['account_number']; // Use _id
        $topup->amount = $validated['amount'];
        $topup->image = $filePath;
        $topup->status = 0; // Pending
        $topup->created_at = now();
        $topup->updated_at = now();
        $topup->save();

        return response()->json([
            'message' => 'Top-up request submitted successfully.',
            'topup' => $topup
        ], 201);
    }

    public function showTopupHistory(Request $request)
    {
        $user = auth()->user();
        if (!$user) {
            abort(403, 'Unauthorized');
        }

        $account = Account::where('user_id', $user->_id)->first();
        $topups = Topup::where('user_id', $user->_id)->with('vehicle')->get();

        return view('topup_history', compact('account', 'topups'));
    }

    public function manageTopups(Request $request)
    {
        $user = Auth::user();
        if (!$user || $user->role != 1) {
            abort(403, 'Unauthorized');
        }

        $topups = Topup::with(['user', 'vehicle'])->get();
        return view('admin.topups', compact('topups'));
    }

    public function updateTopupStatus(Request $request, $topupId)
    {
        $user = auth()->user();
        if (!$user || $user->role != 1) {
            abort(403, 'Unauthorized');
        }

        $validated = $request->validate([
            'status' => 'required|in:1,2',
        ]);

        $topup = Topup::find($topupId);
        if (!$topup) {
            return response()->json(['message' => 'Top-up not found.'], 404);
        }

        $topup->status = $validated['status'];
        $topup->updated_at = now();

        if ($validated['status'] == 1) { // Approved
            $account = Account::where('_id', $topup->account_number)->first();
            if ($account) {
                $account->balance += $topup->amount;
                $account->updated_at = now();
                $account->save();
            }
        }

        $topup->save();

        return response()->json([
            'message' => 'Top-up status updated successfully.',
            'topup' => $topup
        ], 200);
    }
}
