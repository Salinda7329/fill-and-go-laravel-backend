<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use MongoDB\Client as MongoClient;
use Illuminate\Support\Facades\Auth;

class CustomerVehicleLogController extends Controller
{
    public function index(Request $request)
    {
        $user = Auth::user();
        if (!$user) {
            return redirect()->route('login')->with('error', 'Please log in.');
        }

        $mongoUri = env('MONGODB_URI', 'mongodb://localhost:27017');
        $mongo = new MongoClient($mongoUri);
        $db = $mongo->fill_and_go;

        // Get all vehicles for the user
        $vehicles = $db->vehicles->find([
            'firebase_uid' => $user->firebase_uid
            // or: 'customeremail' => $user->email
        ])->toArray();

        // Normalize vehicle numbers (remove spaces, uppercase)
        $vehicleNumbers = array_map(function ($v) {
            return strtoupper(str_replace(' ', '', $v->vehicle_number ?? $v['vehicle_number'] ?? ''));
        }, $vehicles);
        $vehicleNumbers = array_filter($vehicleNumbers);

        // Fetch logs for normalized vehicle numbers
        $logs = [];
        if (!empty($vehicleNumbers)) {
            $allLogs = $db->vehicle_logs->find([], [
                'sort' => ['gate_open_time' => -1]
            ])->toArray();

            // Normalize and filter logs
            $logs = array_filter($allLogs, function ($log) use ($vehicleNumbers) {
                $logNumber = strtoupper(str_replace(' ', '', $log->vehicle_number ?? $log['vehicle_number'] ?? ''));
                return in_array($logNumber, $vehicleNumbers);
            });
        }

        return view('customer.vehicle_logs', [
            'logs' => $logs,
            'user' => $user
        ]);
    }
}
