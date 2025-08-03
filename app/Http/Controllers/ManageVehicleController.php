<?php

namespace App\Http\Controllers;

use App\Models\Account;
use App\Models\Vehicle;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use MongoDB\Laravel\Eloquent\Casts\ObjectId;


class ManageVehicleController extends Controller
{
    public function showRegisterVehicleForm(Request $request)
    {
        if (!Auth::check()) {
            return redirect('/login')->with('error', 'Please log in to register a vehicle.');
        }
        return view('vehicle_registration');
    }

    public function registerVehicle(Request $request)
    {
        try {
            // Validate the request
            $validated = $request->validate([
                'vehicle_number' => 'required|string',
                'fuel_type' => 'required|in:Petrol,Diesel',
                'customeremail' => 'required|email',
                'firebase_uid' => 'required|string',
            ]);

            $user = Auth::user();
            if (!$user || $user->firebase_uid !== $validated['firebase_uid']) {
                return response()->json([
                    'success' => false,
                    'message' => 'Unauthorized. Invalid user session.'
                ], 401);
            }

            // Normalize vehicle number
            $vehicleNumber = strtoupper(str_replace(' ', '', $validated['vehicle_number']));

            // Check if vehicle exists
            if (Vehicle::where('vehicle_number', $vehicleNumber)->exists()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Vehicle number already registered.'
                ], 422);
            }

            // Create account for the customer
            $account = Account::firstOrCreate(
                ['user_id' => $user->_id],
                [
                    '_id' => new ObjectId(),
                    'user_id' => $user->_id,
                    'balance' => 0.00,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]
            );
            Log::info('Account created or retrieved for user', [
                'user_id' => $user->_id,
                'account_id' => $account->_id,
            ]);

            // Create vehicle
            $vehicle = Vehicle::create([
                '_id' => new ObjectId(),
                'vehicle_number' => $vehicleNumber,
                'fuel_type' => $validated['fuel_type'],
                'firebase_uid' => $validated['firebase_uid'],
                'customeremail' => $validated['customeremail'],
                'user_id' => $user->_id,
                'status' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            Log::info('Vehicle registered successfully', [
                'vehicle_id' => $vehicle->_id,
                'vehicle_number' => $vehicleNumber,
                'user_id' => $user->_id,
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Vehicle registered successfully.',
                'vehicle' => $vehicle,
                'account_id' => $account->_id,
            ], 201);
        } catch (\Exception $e) {
            Log::error('Vehicle registration error: ' . $e->getMessage(), [
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'trace' => $e->getTraceAsString(),
                'request' => $request->all(),
            ]);
            return response()->json([
                'success' => false,
                'message' => 'Server error: ' . $e->getMessage(),
            ], 500);
        }
    }

    public function customerVehicles()
    {
        $user = Auth::user();

        // Assuming you have a Vehicle model related to the user
        $vehicles = \App\Models\Vehicle::where('user_id', $user->_id)->get();

        return view('customer.myvehicles', compact('vehicles'));
    }
}
