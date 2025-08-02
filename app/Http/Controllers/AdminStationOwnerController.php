<?php

namespace App\Http\Controllers;

use App\Models\User;

class AdminStationOwnerController extends Controller
{
    public function pending()
    {
        $owners = User::where('role', 2)->where('status', 0)->get();
        return view('admin.stationowners_pending', compact('owners'));
    }

    public function approve($id)
    {
        $owner = User::findOrFail($id);
        $owner->status = 1;
        $owner->save();
        // Optionally send email
        return redirect()->back()->with('success', 'Station Owner approved.');
    }

    public function reject($id)
    {
        $owner = User::findOrFail($id);
        $owner->status = 0;
        $owner->save();
        // Optionally send email
        return redirect()->back()->with('error', 'Station Owner rejected.');
    }
}
