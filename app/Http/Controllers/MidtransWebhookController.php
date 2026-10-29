<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Penjualan;
use App\Models\Pembayaran;
use Illuminate\Support\Facades\Log;

class MidtransWebhookController extends Controller
{
    /**
     * Menangani webhook HTTP notification resmi dari Midtrans Server
     */
    public function handle(Request $request)
    {
        $payload = $request->all();
        Log::info('Midtrans Webhook Received:', $payload);

        $orderId = $payload['order_id'] ?? null;
        $statusCode = $payload['status_code'] ?? null;
        $grossAmount = $payload['gross_amount'] ?? null;
        $signatureKey = $payload['signature_key'] ?? null;
        $transactionStatus = $payload['transaction_status'] ?? null;
        $fraudStatus = $payload['fraud_status'] ?? null;
        $paymentType = $payload['payment_type'] ?? 'qris';

        if (!$orderId || !$statusCode || !$grossAmount || !$signatureKey) {
            return response()->json(['message' => 'Invalid payload'], 400);
        }

        // Verifikasi Signature Key Midtrans: SHA512(order_id + status_code + gross_amount + ServerKey)
        $serverKey = config('midtrans.server_key');
        $validSignature = hash('sha512', $orderId . $statusCode . $grossAmount . $serverKey);

        if ($signatureKey !== $validSignature) {
            Log::warning('Midtrans Webhook: Invalid signature', [
                'expected' => $validSignature,
                'received' => $signatureKey,
            ]);
            return response()->json(['message' => 'Invalid signature'], 403);
        }

        $penjualan = Penjualan::with('pembayaran')->where('id_penjualan', $orderId)->first();
        if (!$penjualan) {
            Log::warning('Midtrans Webhook: Penjualan not found: ' . $orderId);
            return response()->json(['message' => 'Order not found'], 404);
        }

        $pembayaran = $penjualan->pembayaran;
        if (!$pembayaran) {
            $pembayaran = new Pembayaran();
            $pembayaran->id_penjualan = $orderId;
            $pembayaran->nominal_bayar = (int) $grossAmount;
            $pembayaran->gateway_provider = 'midtrans';
        }

        // Simpan data transaksi
        $pembayaran->transaction_id = $payload['transaction_id'] ?? null;
        $pembayaran->metode_pembayaran = $paymentType;
        $pembayaran->payload_callback = json_encode($payload);

        // Jika ada VA number dari bank transfer
        if (isset($payload['va_numbers'][0]['va_number'])) {
            $pembayaran->va_number = $payload['va_numbers'][0]['va_number'];
        }

        // Pemetaan status transaksi Midtrans
        // settlement / capture (accept) -> LUNAS
        if ($transactionStatus === 'capture') {
            if ($fraudStatus === 'accept') {
                $pembayaran->status_pembayaran = 'settlement';
                $pembayaran->waktu_bayar = now();
            } else {
                $pembayaran->status_pembayaran = 'pending';
            }
        } elseif ($transactionStatus === 'settlement') {
            $pembayaran->status_pembayaran = 'settlement';
            $pembayaran->waktu_bayar = now();
        } elseif (in_array($transactionStatus, ['cancel', 'deny'])) {
            $pembayaran->status_pembayaran = 'cancel';
        } elseif ($transactionStatus === 'expire') {
            $pembayaran->status_pembayaran = 'expire';
        } elseif ($transactionStatus === 'pending') {
            $pembayaran->status_pembayaran = 'pending';
        }

        // Menyimpan data memicu trigger database trg_proses_settlement_pembayaran
        $pembayaran->save();

        return response()->json(['message' => 'Webhook handled successfully'], 200);
    }
}
