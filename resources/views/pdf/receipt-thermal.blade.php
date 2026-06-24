<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Struk #{{ $transaction->id }}</title>
    <style>
        @page { margin: 10px; }
        body {
            font-family: 'Helvetica', 'Arial', sans-serif;
            font-size: 11px;
            color: #000;
            line-height: 1.4;
            margin: 0;
            padding: 0;
            width: 100%;
        }
        .center { text-align: center; }
        .right { text-align: right; }
        .left { text-align: left; }
        .bold { font-weight: bold; }
        
        .header { margin-bottom: 10px; border-bottom: 1px dashed #000; padding-bottom: 10px; }
        .logo { max-width: 60px; margin-bottom: 5px; }
        .shop-name { font-size: 14px; font-weight: bold; text-transform: uppercase; }
        .shop-address { font-size: 10px; }
        
        .info { margin-bottom: 10px; font-size: 10px; }
        
        table { width: 100%; border-collapse: collapse; margin-bottom: 10px; }
        th { border-top: 1px dashed #000; border-bottom: 1px dashed #000; padding: 4px 0; text-align: left; font-size: 10px;}
        td { padding: 4px 0; vertical-align: top; font-size: 10px;}
        
        .totals { border-top: 1px dashed #000; padding-top: 5px; margin-bottom: 15px; }
        .total-row { display: table; width: 100%; font-size: 11px;}
        .total-label { display: table-cell; text-align: left; }
        .total-val { display: table-cell; text-align: right; font-weight: bold; }
        
        .footer { text-align: center; font-size: 10px; border-top: 1px dashed #000; padding-top: 10px; }
    </style>
</head>
<body>

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

    <div class="header center">
        @if($logoBase64)
            <img src="{{ $logoBase64 }}" class="logo" alt="Logo">
        @endif
        <div class="shop-name">{{ $shopName }}</div>
        <div class="shop-address">{{ $shopAddress }}</div>
    </div>

    <div class="info">
        <table style="margin-bottom: 0;">
            <tr>
                <td style="padding: 0;">TRX:</td>
                <td style="padding: 0;" class="right">{{ $transaction->id }}</td>
            </tr>
            <tr>
                <td style="padding: 0;">TGL:</td>
                <td style="padding: 0;" class="right">{{ $transaction->created_at->format('d/m/Y H:i') }}</td>
            </tr>
            <tr>
                <td style="padding: 0;">KASIR:</td>
                <td style="padding: 0;" class="right">{{ $transaction->user->name ?? 'Admin' }}</td>
            </tr>
        </table>
    </div>

    <table>
        <thead>
            <tr>
                <th width="45%">Item</th>
                <th width="15%" class="center">Qty</th>
                <th width="40%" class="right">Total</th>
            </tr>
        </thead>
        <tbody>
            @foreach($transaction->details as $item)
            <tr>
                <td>{{ substr($item->product->name ?? 'Item', 0, 16) }}<br><small>Rp{{ number_format($item->subtotal / $item->qty, 0, ',', '.') }}</small></td>
                <td class="center">{{ $item->qty }}</td>
                <td class="right">Rp{{ number_format($item->subtotal, 0, ',', '.') }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <div class="totals">
        <table style="margin:0;">
            <tr>
                <td class="bold">GRAND TOTAL</td>
                <td class="bold right" style="font-size: 13px;">Rp{{ number_format($transaction->grand_total, 0, ',', '.') }}</td>
            </tr>
        </table>
    </div>

    <div class="footer">
        Terima kasih atas kunjungan Anda.<br>
        Barang yang sudah dibeli tidak dapat ditukar.
    </div>

</body>
</html>
