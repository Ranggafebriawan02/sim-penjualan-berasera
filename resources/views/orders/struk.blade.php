@extends('layouts.app')

@section('title', 'Struk Pembelian Beras - #' . $penjualan->id_penjualan)

@section('content')
<div class="max-w-xl mx-auto px-4 py-8">
    <div class="flex items-center justify-between mb-6 no-print">
        <a href="{{ route('orders.riwayat') }}" class="text-sm font-semibold text-gray-600 hover:text-emerald-600 flex items-center gap-1.5 transition">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
            Kembali ke Riwayat
        </a>
        <button onclick="window.print()" class="px-4 py-2 bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-semibold rounded-xl shadow-sm transition flex items-center gap-2">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/></svg>
            Cetak Struk
        </button>
    </div>

    <!-- Struk Card -->
    <div class="bg-white rounded-3xl border border-gray-200 shadow-lg p-8 relative overflow-hidden" id="struk-area">
        <!-- Header Toko / Gapsera -->
        <div class="text-center pb-6 border-b border-dashed border-gray-200">
            <div class="inline-flex items-center justify-center w-14 h-14 rounded-2xl bg-emerald-50 text-emerald-600 text-2xl font-black mb-3">
                🌾
            </div>
            <h2 class="text-xl font-black text-gray-900 tracking-tight">POKTAN GAPSERA SEJAHTERA MANDIRI</h2>
            <p class="text-xs text-gray-500 mt-0.5">Sistem Informasi Manajemen & Penjualan Beras Berkualitas</p>
            <p class="text-xs text-gray-400 mt-1">Desa Sumber Makmur • Kontak: 0812-3456-7890</p>
        </div>

        <!-- Order Meta -->
        <div class="py-5 border-b border-dashed border-gray-200 text-xs text-gray-600 space-y-1.5">
            <div class="flex justify-between">
                <span class="text-gray-400">No. Transaksi</span>
                <span class="font-mono font-bold text-gray-900">{{ $penjualan->id_penjualan }}</span>
            </div>
            <div class="flex justify-between">
                <span class="text-gray-400">Tanggal & Waktu</span>
                <span class="font-medium text-gray-800">{{ \Carbon\Carbon::parse($penjualan->tgl_jual ?? $penjualan->created_at)->format('d/m/Y') }}</span>
            </div>
            <div class="flex justify-between">
                <span class="text-gray-400">Pelanggan</span>
                <span class="font-semibold text-gray-900">{{ $penjualan->customer->nama_customer ?? $penjualan->customer->nama_lengkap ?? 'Pelanggan Toko' }}</span>
            </div>
            <div class="flex justify-between">
                <span class="text-gray-400">Metode Bayar</span>
                <span class="font-medium text-gray-800 uppercase">{{ $penjualan->pembayaran->metode_pembayaran ?? $penjualan->metode_transaksi ?? 'Midtrans QRIS' }}</span>
            </div>
            <div class="flex justify-between">
                <span class="text-gray-400">Status Pembayaran</span>
                @php
                    $status = $penjualan->pembayaran->status_pembayaran ?? 'pending';
                @endphp
                <span class="font-bold uppercase {{ in_array($status, ['settlement', 'berhasil', 'lunas']) ? 'text-emerald-600' : (in_array($status, ['gagal', 'cancel', 'batal']) ? 'text-red-500' : 'text-amber-500') }}">
                    {{ $status }}
                </span>
            </div>
        </div>

        <!-- Rincian Item -->
        <div class="py-5 border-b border-dashed border-gray-200">
            <p class="text-xs font-bold text-gray-400 uppercase tracking-wider mb-3">Item Pembelian</p>
            <div class="space-y-3">
                @foreach(($penjualan->details ?? $penjualan->detailPenjualans ?? []) as $item)
                    <div class="text-xs">
                        <div class="flex justify-between font-semibold text-gray-800">
                            <span>{{ $item->produk->nama_produk ?? 'Beras Poktan' }}</span>
                            <span>Rp {{ number_format($item->sub_total ?? $item->subtotal ?? 0, 0, ',', '.') }}</span>
                        </div>
                        <div class="text-gray-400 flex justify-between mt-0.5">
                            <span>{{ $item->jumlah }} {{ $item->produk->satuan ?? 'Pcs' }} x Rp {{ number_format($item->harga_satuan, 0, ',', '.') }}</span>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>

        <!-- Totaling -->
        <div class="py-5 border-b border-dashed border-gray-200 text-xs space-y-2">
            <div class="flex justify-between text-gray-500">
                <span>Subtotal Belanja</span>
                <span class="font-medium text-gray-800">Rp {{ number_format($penjualan->total ?? $penjualan->total_harga ?? 0, 0, ',', '.') }}</span>
            </div>
            <div class="flex justify-between text-gray-500">
                <span>Biaya Layanan / Admin</span>
                <span class="font-medium text-emerald-600">Rp 0 (Gratis)</span>
            </div>
            <div class="flex justify-between text-base font-black text-gray-900 pt-2 border-t border-gray-100">
                <span>TOTAL BAYAR</span>
                <span class="text-emerald-700">Rp {{ number_format($penjualan->total ?? $penjualan->total_harga ?? 0, 0, ',', '.') }}</span>
            </div>
        </div>

        <!-- Footer / Pesan -->
        <div class="pt-6 text-center text-xs text-gray-400">
            <p class="font-semibold text-gray-600">Terima kasih atas kepercayaan Anda!</p>
            <p class="mt-1">Beras pilihan sehat langsung dari lumbung petani Gapsera.</p>
            <div class="mt-4 pt-3 border-t border-gray-100 font-mono text-[10px] text-gray-400">
                SIM Penjualan Beras v2.0 • Midtrans Sandbox Ready
            </div>
        </div>
    </div>
</div>

<style>
@media print {
    .no-print {
        display: none !important;
    }
    body {
        background: white !important;
    }
    #struk-area {
        box-shadow: none !important;
        border: 1px solid #ddd !important;
        padding: 20px !important;
        max-width: 100% !important;
    }
}
</style>
@endsection
