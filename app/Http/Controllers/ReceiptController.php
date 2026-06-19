<?php

namespace App\Http\Controllers;

use App\Models\Transaction;
use App\Models\Shop;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;

class ReceiptController extends Controller
{
    /**
     * Generate PDF struk — dipanggil dari cashier (setelah bayar)
     * maupun dari halaman transactions (per baris).
     *
     * Query param: ?size=thermal (default) | a4
     */
    public function download(Transaction $transaction, Request $request)
    {
        $size = $request->query('size', 'thermal');

        // Load semua relasi yang dibutuhkan struk
        $transaction->load([
            'details.product',
            'user',
        ]);

        $shop = Shop::with('setting')->first();

        $data = [
            'transaction' => $transaction,
            'shop'        => $shop,
            'shopName'    => $shop?->name ?? 'Toko Kami',
            'shopAddress' => $shop?->address ?? 'Alamat Toko',
            'size'        => $size,
        ];

        $pdf = $size === 'a4'
            ? $this->buildA4($data)
            : $this->buildThermal($data);

        $filename = 'struk-' . $transaction->id . '-' . now()->format('Ymd') . '.pdf';

        return $pdf->download($filename);
    }

    /**
     * Stream (preview di browser tab baru)
     */
    public function preview(Transaction $transaction, Request $request)
    {
        $size = $request->query('size', 'thermal');

        $transaction->load(['details.product', 'user']);
        $shop = Shop::with('setting')->first();

        $data = [
            'transaction' => $transaction,
            'shop'        => $shop,
            'shopName'    => $shop?->name ?? 'Toko Kami',
            'shopAddress' => $shop?->address ?? 'Alamat Toko',
            'size'        => $size,
        ];

        $pdf = $size === 'a4'
            ? $this->buildA4($data)
            : $this->buildThermal($data);

        $filename = 'struk-' . $transaction->id . '.pdf';

        return $pdf->stream($filename);
    }

    // ── Private builders ──────────────────────────────────

    private function buildThermal(array $data)
    {
        $pdf = Pdf::loadView('pdf.receipt-thermal', $data);

        // 80mm thermal width = ~226.77pt, height auto
        $pdf->setPaper([0, 0, 226.77, 600], 'portrait');

        $pdf->setOptions([
            'isHtml5ParserEnabled' => true,
            'isRemoteEnabled'      => true,
            'defaultFont'          => 'DejaVu Sans Mono',
            'dpi'                  => 96,
        ]);

        return $pdf;
    }

    private function buildA4(array $data)
    {
        $pdf = Pdf::loadView('pdf.receipt-a4', $data);

        $pdf->setPaper('a4', 'portrait');

        $pdf->setOptions([
            'isHtml5ParserEnabled' => true,
            'isRemoteEnabled'      => true,
            'defaultFont'          => 'DejaVu Sans',
            'dpi'                  => 96,
        ]);

        return $pdf;
    }
}
