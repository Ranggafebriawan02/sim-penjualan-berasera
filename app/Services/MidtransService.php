<?php

namespace App\Services;

use Midtrans\Config;
use Midtrans\Snap;
use Midtrans\Transaction;
use App\Models\Penjualan;
use Illuminate\Support\Facades\Log;

class MidtransService
{
    public function __construct()
    {
        $this->init();
    }

    protected function init(): void
    {
        Config::$serverKey = config('midtrans.server_key');
        Config::$isProduction = (bool) config('midtrans.is_production', false);
        Config::$isSanitized = (bool) config('midtrans.is_sanitized', true);
        Config::$is3ds = (bool) config('midtrans.is_3ds', true);

        // Windows localhost SSL fix: cegah error 'unable to get local issuer certificate'
        // Harus sertakan key CURLOPT_HTTPHEADER karena ApiRequestor mengakses Config::$curlOptions[CURLOPT_HTTPHEADER]
        Config::$curlOptions = [
            CURLOPT_HTTPHEADER => [],
            CURLOPT_SSL_VERIFYPEER => false,
            CURLOPT_SSL_VERIFYHOST => 0,
        ];
    }

    /**
     * Membuat Snap Token untuk transaksi penjualan beras
     */
    public function createSnapToken(Penjualan $penjualan): ?string
    {
        $this->init();

        $items = [];
        foreach ($penjualan->details as $detail) {
            $items[] = [
                'id' => (string) $detail->id_produk,
                'price' => (int) $detail->harga_satuan,
                'quantity' => (int) $detail->jumlah,
                'name' => mb_substr($detail->produk->nama_produk ?? 'Beras Organik', 0, 50),
            ];
        }

        // Jika ada ongkir dan > 0
        if ($penjualan->ongkir > 0) {
            $items[] = [
                'id' => 'ONGKIR',
                'price' => (int) $penjualan->ongkir,
                'quantity' => 1,
                'name' => 'Ongkos Kirim',
            ];
        }

        // Jika ada diskon poin
        if ($penjualan->diskon_poin > 0) {
            $items[] = [
                'id' => 'DISKON-POIN',
                'price' => -1 * (int) $penjualan->diskon_poin,
                'quantity' => 1,
                'name' => 'Potongan Poin Loyalitas',
            ];
        }

        // Pastikan total item_details persis sama dengan gross_amount
        $grossAmount = (int) $penjualan->total;

        $params = [
            'transaction_details' => [
                'order_id' => $penjualan->id_penjualan,
                'gross_amount' => $grossAmount,
            ],
            'item_details' => $items,
            'customer_details' => [
                'first_name' => $penjualan->customer->nama_customer ?? 'Pelanggan',
                'email' => $penjualan->customer->email ?? 'customer@berasera.id',
                'phone' => $penjualan->customer->telepon ?? '081234567890',
                'billing_address' => [
                    'first_name' => $penjualan->customer->nama_customer ?? 'Pelanggan',
                    'address' => $penjualan->alamat_pengiriman ?? 'Alamat Pelanggan',
                ],
            ],
            'callbacks' => [
                'finish' => route('payment.show', $penjualan->id_penjualan),
            ],
        ];

        try {
            $snapToken = Snap::getSnapToken($params);
            return $snapToken;
        } catch (\Exception $e) {
            Log::error('Gagal membuat Midtrans Snap Token: ' . $e->getMessage(), [
                'id_penjualan' => $penjualan->id_penjualan,
                'trace' => $e->getTraceAsString()
            ]);
            return null;
        }
    }

    /**
     * Cek status transaksi langsung ke Midtrans API
     */
    public function checkStatus(string $orderId)
    {
        $this->init();
        try {
            return Transaction::status($orderId);
        } catch (\Exception $e) {
            Log::error('Gagal memeriksa status Midtrans untuk ' . $orderId . ': ' . $e->getMessage());
            return null;
        }
    }
}
