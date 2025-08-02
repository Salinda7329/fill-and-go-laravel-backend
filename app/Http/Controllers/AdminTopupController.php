<?php

namespace App\Http\Controllers;

use App\Models\Topup;
use App\Models\Account;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class AdminTopupController extends Controller
{
    public function index()
    {
        $topups = Topup::where('status', 'pending')->with('user')->get();
        return view('admin.managetopups', compact('topups'));
    }

    public function approve($id)
    {
        $topup = Topup::findOrFail($id);

        // Find account of user
        $account = Account::firstOrCreate(
            ['user_id' => $topup->user_id],
            ['balance' => 0]
        );

        $account->balance += (float)$topup->amount;
        $account->save();

        $topup->status = 'approved';
        $topup->save();

        return redirect()->back()->with('success', 'Topup approved and balance updated.');
    }

    public function reject($id)
    {
        $topup = Topup::findOrFail($id);
        $topup->status = 'rejected';
        $topup->save();

        return redirect()->back()->with('error', 'Topup rejected.');
    }

    public function updateAmount(Request $request, $id)
    {
        $request->validate([
            'detected_amount' => 'required|numeric|min:0.01',
        ]);

        $topup = Topup::findOrFail($id);
        $topup->detected_amount = $request->detected_amount;
        // Update the main amount as well, so approval will use this
        $topup->amount = $request->detected_amount;
        $topup->save();

        return response()->json(['success' => true]);
    }
}
