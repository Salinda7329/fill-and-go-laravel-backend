<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Vehicle;
use App\Models\User;
use MongoDB\BSON\ObjectId;

class ManageVehicleController extends Controller
{
    public function registerVehicle(Request $request)
    {
        // Validate the request
        $validated = $request->validate([
            'vehicle_number' => 'required|string',
            'fuel_type' => 'required|in:Petrol,Diesel',
            'customeremail' => 'required|email',
            'firebase_uid' => 'required|string',
        ]);

        // Normalize vehicle number (remove spaces and convert to uppercase)
        $vehicleNumber = strtoupper(str_replace(' ', '', $validated['vehicle_number']));

        // Check if vehicle already exists
        $existingVehicle = Vehicle::where('vehicle_number', $vehicleNumber)->first();

        if ($existingVehicle) {
            return response()->json([
                'message' => 'Vehicle number already registered.'
            ], 422);
        }

        // Find the user by firebase_uid to get their MongoDB _id
        $user = User::where('firebase_uid', $validated['firebase_uid'])->first();

        if (!$user) {
            return response()->json([
                'message' => 'User not found.'
            ], 404);
        }

        // Create new vehicle record
        $vehicle = new Vehicle();
        $vehicle->vehicle_number = $vehicleNumber;
        $vehicle->fuel_type = $validated['fuel_type'];
        $vehicle->firebase_uid = $validated['firebase_uid'];
        $vehicle->customeremail = $validated['customeremail'];
        $vehicle->user_id = $user->_id; // Store the user's MongoDB _id
        $vehicle->status = 1;
        $vehicle->created_at = now();
        $vehicle->updated_at = now();
        $vehicle->save();

        return response()->json([
            'message' => 'Vehicle registered successfully.',
            'vehicle' => $vehicle
        ], 201);
    }
}
