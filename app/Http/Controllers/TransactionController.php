<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Transaction;
use App\Models\TransactionDetail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class TransactionController extends Controller
{
    public function store(Request $request, \App\Services\MidtransService $midtransService)
    {
        $request->validate([
            'cart' => 'required|array|min:1',
            'cart.*.id' => 'required|exists:products,id',
            'cart.*.qty' => 'required|integer|min:1',
            'cart.*.price' => 'required|integer|min:0',
            'grand_total' => 'required|integer|min:0',
            'payment_amount' => 'required|numeric|min:0',
            'payment_method' => 'required|string|in:cash,qris,debit',
        ]);

        try {
            DB::beginTransaction();

            // Create Transaction
            $transaction = Transaction::create([
                'user_id' => auth()->id(),
                'grand_total' => $request->grand_total,
                'payment_method' => $request->payment_method,
                'payment_status' => $request->payment_method === 'cash' ? 'settlement' : 'pending',
            ]);

            $itemDetails = [];

            foreach ($request->cart as $item) {
                // Deduct stock
                $product = Product::lockForUpdate()->find($item['id']);
                
                if ($product->stock < $item['qty']) {
                    throw new \Exception("Stok {$product->name} tidak mencukupi (Sisa: {$product->stock})");
                }
                
                $product->decrement('stock', $item['qty']);

                // Create Detail
                TransactionDetail::create([
                    'transaction_id' => $transaction->id,
                    'product_id' => $item['id'],
                    'qty' => $item['qty'],
                    'subtotal' => $item['price'] * $item['qty'],
                ]);

                // Map item details for Midtrans
                $itemDetails[] = [
                    'id' => (string) $product->id,
                    'price' => (int) $item['price'],
                    'quantity' => (int) $item['qty'],
                    'name' => substr($product->name, 0, 50),
                ];
            }

            // Generate Midtrans Snap Token for non-cash payments
            $snapToken = null;
            if (in_array($request->payment_method, ['qris', 'debit'])) {
                $transactionDetails = [
                    'order_id' => 'TRX-' . dechex($transaction->id) . '-' . time(),
                    'gross_amount' => (int) $request->grand_total,
                ];

                $snapToken = $midtransService->createSnapToken($transactionDetails, $itemDetails);
                
                // Update transaction with snap token
                $transaction->update(['snap_token' => $snapToken]);
            }

            DB::commit();

            return response()->json([
                'message' => 'Transaksi berhasil',
                'transaction_id' => $transaction->id,
                'snap_token' => $snapToken,
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'message' => $e->getMessage()
            ], 422);
        }
    }

    public function destroy(Transaction $transaction)
    {
        // Delete transaction (cascades to details)
        // Usually, POS systems don't allow hard delete, but for this app requirements:
        $transaction->delete();

        return response()->json([
            'message' => 'Transaksi berhasil dihapus'
        ]);
    }
}
