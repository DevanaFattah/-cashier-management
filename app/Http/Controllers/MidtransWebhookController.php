<?php

namespace App\Http\Controllers;

use App\Models\Transaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class MidtransWebhookController extends Controller
{
    /**
     * Handle incoming payment notification from Midtrans.
     * Midtrans will POST to this endpoint whenever payment status changes.
     */
    public function handle(Request $request)
    {
        $serverKey = config('midtrans.server_key');

        // --- 1. Validate Midtrans Signature Key ---
        $orderId       = $request->input('order_id');
        $statusCode    = $request->input('status_code');
        $grossAmount   = $request->input('gross_amount');
        $signatureKey  = $request->input('signature_key');

        $expectedSignature = hash('sha512', $orderId . $statusCode . $grossAmount . $serverKey);

        if ($signatureKey !== $expectedSignature) {
            Log::warning('Midtrans webhook: Invalid signature', [
                'order_id' => $orderId,
                'status_code' => $statusCode,
                'gross_amount' => $grossAmount,
                'received_sig' => $signatureKey,
                'expected_sig' => $expectedSignature,
            ]);
            return response()->json(['message' => 'Invalid signature'], 403);
        }

        // --- 2. Extract transaction ID from order_id (format: TRX-{id}-{timestamp}) ---
        $parts = explode('-', $orderId);
        // Let's get the middle part: between TRX and the timestamp. 
        // If order_id is TRX-22ef-1717144800, parts are ['TRX', '22ef', '1717144800'].
        // If there are other hyphens, we want to grab everything after the first element ('TRX') and before the last element (timestamp).
        // To be safe and simple: the transaction ID is the second element (index 1) in standard format.
        // We find the transaction by id (which is either decimal or hexadecimal, e.g., using find() or where('id', ...))
        $transactionId = $parts[1] ?? null;

        if (!$transactionId) {
            Log::warning('Midtrans webhook: Cannot parse transaction ID', ['order_id' => $orderId]);
            return response()->json(['message' => 'Invalid order_id format'], 422);
        }

        // Search by ID. Since Laravel ID might be primary key as auto-increment (integer) or string.
        // If the ID in the DB is an auto-increment integer, but order_id contains hex (e.g. from a past migration or custom format),
        // we should try to search by hexdec($transactionId) if it's not a numeric string, or just find it directly.
        // Let's check if the ID is hexadecimal. If it's hex, hexdec('22ef') = 8943.
        $searchId = $transactionId;
        if (!is_numeric($transactionId) && ctype_xdigit($transactionId)) {
            $searchId = hexdec($transactionId);
        }

        $transaction = Transaction::find($searchId);

        if (!$transaction) {
            Log::warning('Midtrans webhook: Transaction not found', ['transaction_id' => $transactionId]);
            return response()->json(['message' => 'Transaction not found'], 404);
        }

        // --- 3. Update payment_status based on Midtrans transaction_status ---
        $transactionStatus = $request->input('transaction_status');
        $fraudStatus       = $request->input('fraud_status');

        $newStatus = match(true) {
            $transactionStatus === 'capture' && $fraudStatus === 'accept' => 'settlement',
            $transactionStatus === 'settlement'                           => 'settlement',
            $transactionStatus === 'pending'                              => 'pending',
            in_array($transactionStatus, ['deny', 'cancel', 'expire'])   => $transactionStatus,
            default                                                       => $transaction->payment_status,
        };

        $transaction->update(['payment_status' => $newStatus]);

        Log::info('Midtrans webhook processed', [
            'transaction_id' => $transactionId,
            'order_id'       => $orderId,
            'status'         => $newStatus,
        ]);

        return response()->json(['message' => 'OK'], 200);
    }
}
