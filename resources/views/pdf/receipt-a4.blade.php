<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Invoice #{{ $transaction->id }}</title>
    <style>
        body { font-family: 'Helvetica', 'Arial', sans-serif; font-size: 12px; color: #333; line-height: 1.5; }
        .container { padding: 20px; }
        .header { text-align: center; margin-bottom: 30px; padding-bottom: 20px; border-bottom: 2px solid #eee; }
        .logo { max-height: 80px; margin-bottom: 10px; }
        .shop-name { font-size: 24px; font-weight: bold; color: #000; text-transform: uppercase; }
        .shop-address { color: #666; font-size: 14px; }
        
        .meta-box { margin-bottom: 30px; width: 100%; }
        .meta-box td { padding: 5px; }
        .meta-title { font-weight: bold; width: 120px; }
        
        .items-table { width: 100%; border-collapse: collapse; margin-bottom: 30px; }
        .items-table th { background: #f8f9fa; padding: 12px; text-align: left; border-bottom: 2px solid #dee2e6; color: #495057; }
        .items-table td { padding: 12px; border-bottom: 1px solid #dee2e6; }
        .items-table th.right, .items-table td.right { text-align: right; }
        .items-table th.center, .items-table td.center { text-align: center; }
        
        .totals-box { width: 40%; float: right; margin-bottom: 30px; }
        .totals-box table { width: 100%; }
        .totals-box td { padding: 8px; }
        .totals-box .grand-total { font-size: 18px; font-weight: bold; border-top: 2px solid #000; }
        
        .footer { clear: both; text-align: center; padding-top: 50px; color: #666; font-size: 12px; }
    </style>
</head>
<body>
    <div class="container">
        @php
            $logoBase64 = '';
            if ($shop && $shop->path_logo) {
                $logoPath = storage_path('app/public/' . $shop->path_logo);
                if (file_exists($logoPath)) {
                    $logoData = base64_encode(file_get_contents($logoPath));
                    $logoBase64 = 'data:image/png;base64,' . $logoData;
                }
            }
        @endphp

        <div class="header">
            @if($logoBase64)
                <img src="{{ $logoBase64 }}" class="logo" alt="Logo">
            @endif
            <div class="shop-name">{{ $shopName }}</div>
            <div class="shop-address">{{ $shopAddress }}</div>
        </div>

        <table class="meta-box">
            <tr>
                <td class="meta-title">INVOICE NO:</td>
                <td>{{ $transaction->id }}</td>
                <td class="meta-title">TANGGAL:</td>
                <td>{{ $transaction->created_at->format('d F Y, H:i') }}</td>
            </tr>
            <tr>
                <td class="meta-title">KASIR:</td>
                <td>{{ $transaction->user->name ?? 'Admin' }}</td>
                <td class="meta-title">METODE BAYAR:</td>
                <td style="text-transform: uppercase;">{{ $transaction->payment_method }}</td>
            </tr>
        </table>

        <table class="items-table">
            <thead>
                <tr>
                    <th width="5%">No</th>
                    <th width="45%">Deskripsi Produk</th>
                    <th width="15%" class="center">Kuantitas</th>
                    <th width="15%" class="right">Harga Satuan</th>
                    <th width="20%" class="right">Subtotal</th>
                </tr>
            </thead>
            <tbody>
                @foreach($transaction->details as $index => $item)
                <tr>
                    <td>{{ $index + 1 }}</td>
                    <td>
                        <strong>{{ $item->product->code ?? '-' }}</strong><br>
                        {{ $item->product->name ?? 'Produk' }}
                    </td>
                    <td class="center">{{ $item->qty }}</td>
                    <td class="right">Rp {{ number_format($item->subtotal / $item->qty, 0, ',', '.') }}</td>
                    <td class="right">Rp {{ number_format($item->subtotal, 0, ',', '.') }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>

        <div class="totals-box">
            <table>
                <tr>
                    <td>Subtotal</td>
                    <td class="right">Rp {{ number_format($transaction->grand_total, 0, ',', '.') }}</td>
                </tr>
                <tr>
                    <td>Diskon</td>
                    <td class="right">Rp 0</td>
                </tr>
                <tr class="grand-total">
                    <td>TOTAL</td>
                    <td class="right">Rp {{ number_format($transaction->grand_total, 0, ',', '.') }}</td>
                </tr>
            </table>
        </div>

        <div class="footer">
            <p>Terima kasih telah berbelanja di {{ $shopName }}</p>
            <p>Dokumen ini adalah bukti pembayaran yang sah. Dicetak oleh sistem pada {{ now()->format('d/m/Y H:i:s') }}</p>
        </div>
    </div>
</body>
</html>
