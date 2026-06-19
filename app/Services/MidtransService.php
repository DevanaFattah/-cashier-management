<?php

namespace App\Services;

use Midtrans\Config;
use Midtrans\Snap;
use Exception;

class MidtransService
{
    public function __construct()
    {
        // Set konfigurasi midtrans dari config file
        Config::$serverKey = config('midtrans.server_key');
        Config::$isProduction = config('midtrans.is_production');
        Config::$isSanitized = config('midtrans.is_sanitized');
        Config::$is3ds = config('midtrans.is_3ds');

        // Bypass SSL certificate verification for local development (Laragon/cURL SSL issue)
        if (!config('midtrans.is_production')) {
            // Midtrans SDK merges curlOptions. We must supply an empty array for CURLOPT_HTTPHEADER (key 10023)
            // to avoid 'Undefined array key 10023' inside ApiRequestor.php line 117.
            Config::$curlOptions = [
                CURLOPT_SSL_VERIFYHOST => 0,
                CURLOPT_SSL_VERIFYPEER => 0,
                CURLOPT_HTTPHEADER => [],
            ];
        }
    }

    /**
     * Buat Snap Token transaksi baru
     *
     * @param array $transactionDetails
     * @param array $itemDetails
     * @param array $customerDetails
     * @return string
     * @throws Exception
     */
    public function createSnapToken(array $transactionDetails, array $itemDetails, array $customerDetails = [])
    {
        $payload = [
            'transaction_details' => $transactionDetails,
            'item_details' => $itemDetails,
        ];

        if (!empty($customerDetails)) {
            $payload['customer_details'] = $customerDetails;
        }

        try {
            return Snap::getSnapToken($payload);
        } catch (Exception $e) {
            throw new Exception('Midtrans Error: ' . $e->getMessage());
        }
    }
}
