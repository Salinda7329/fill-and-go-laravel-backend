<?php

namespace App\Http\Controllers;

use App\Models\Topup;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use thiagoalessio\TesseractOCR\TesseractOCR;

class PaymentProofController extends Controller
{
    public function showForm()
    {
        return view('customer.payment_proof_upload');
    }

    public function store(Request $request)
    {
        $request->validate([
            'amount' => 'required|numeric|min:1',
            'proof_image' => 'required|image|max:5120', // 5 MB
        ]);

        $user = Auth::user();

        // Save uploaded file
        $path = $request->file('proof_image')->store('topups', 'public');

        // Extract text from the uploaded image using Tesseract
        $imageFullPath = storage_path('app/public/' . $path);
        $extractedText = (new TesseractOCR($imageFullPath))
            ->executable('C:\Program Files\Tesseract-OCR\tesseract.exe') // Add this line
            ->lang('eng')
            ->run();
        Log::info('Extracted text from proof: ' . $extractedText);

        // Use regex to detect a number that looks like an amount
        preg_match('/\d+[.,]\d{2}/', $extractedText, $matches);
        $detectedAmount = $matches[0] ?? null;

        // Save the topup request to MongoDB
        $topup = Topup::create([
            'user_id' => $user->_id,
            'amount' => $request->amount,
            'detected_amount' => $detectedAmount,
            'reference_number' => $request->reference_number ?? null,
            'proof_image' => $path,
            'status' => 'pending',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Proof uploaded successfully. Awaiting admin review.',
            'detected_amount' => $detectedAmount,
        ]);
    }
}
