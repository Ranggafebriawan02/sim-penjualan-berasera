@extends('layouts.app')

@section('title', 'Riwayat Pesanan Saya — Berasera')

@section('content')
<style>
    .history-container {
        max-width: 1080px;
        margin: 40px auto;
        padding: 0 24px;
    }

    .history-header {
        margin-bottom: 28px;
        display: flex;
        justify-content: space-between;
        align-items: flex-end;
        flex-wrap: wrap;
        gap: 16px;
    }

    .history-header h1 {
        font-size: 26px;
        font-weight: 800;
        color: #064e3b;
        letter-spacing: -0.5px;
    }

    .history-header p {
        font-size: 13.5px;
        color: #64748b;
        margin-top: 4px;
    }

    /* Card Order */
    .order-card {
        background: #ffffff;
        border: 1px solid #e2e8f0;
        border-radius: 18px;
        padding: 24px;
        margin-bottom: 20px;
        box-shadow: 0 4px 16px rgba(0, 0, 0, 0.03);
        transition: transform 0.2s, box-shadow 0.2s;
    }

    .order-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 10px 24px rgba(0, 0, 0, 0.06);
    }

    .order-head {
        display: flex;
        justify-content: space-between;
        align-items: center;
        flex-wrap: wrap;
        gap: 12px;
        padding-bottom: 16px;
        border-bottom: 1px dashed #e2e8f0;
    }

    .order-meta {
        display: flex;
        align-items: center;
        gap: 12px;
        flex-wrap: wrap;
    }

    .order-id {
        font-family: 'JetBrains Mono', monospace;
        font-weight: 800;
        font-size: 14px;
        color: #065f46;
        background: #ecfdf5;
        padding: 4px 10px;
        border-radius: 8px;
        border: 1px solid #a7f3d0;
    }

    .order-date {
        font-size: 12.5px;
        color: #64748b;
        font-weight: 500;
    }

    .badge-status {
        font-size: 11.5px;
        font-weight: 700;
        padding: 4px 12px;
        border-radius: 20px;
        display: inline-flex;
        align-items: center;
        gap: 6px;
    }

    .badge-paid {
        background: #dcfce7;
        color: #15803d;
        border: 1px solid #bbf7d0;
    }

    .badge-pending {
        background: #fef3c7;
        color: #b45309;
        border: 1px solid #fde68a;
    }

    .badge-failed {
        background: #fee2e2;
        color: #b91c1c;
        border: 1px solid #fecaca;
    }

    /* Product items */
    .order-items {
        padding: 16px 0;
        display: flex;
        flex-direction: column;
        gap: 14px;
    }

    .order-item-row {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 14px;
    }

    .product-info-box {
        display: flex;
        align-items: center;
        gap: 12px;
    }

    .product-avatar {
        width: 44px;
        height: 44px;
        background: #f1f5f9;
        border-radius: 10px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 20px;
        border: 1px solid #e2e8f0;
    }

    .product-text h4 {
        font-size: 14px;
        font-weight: 700;
        color: #0f172a;
        margin-bottom: 2px;
    }

    .product-text p {
        font-size: 12px;
        color: #64748b;
    }

    .item-subtotal {
        font-size: 13.5px;
        font-weight: 700;
        color: #0f172a;
    }

    /* Footer card */
    .order-foot {
        display: flex;
        justify-content: space-between;
        align-items: center;
        flex-wrap: wrap;
        gap: 16px;
        padding-top: 16px;
        border-top: 1px solid #f1f5f9;
    }

    .total-price-box .lbl {
        font-size: 11.5px;
        font-weight: 600;
        color: #64748b;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }

    .total-price-box .val {
        font-size: 22px;
        font-weight: 800;
        color: #064e3b;
        line-height: 1.1;
    }

    .order-actions {
        display: flex;
        align-items: center;
        gap: 8px;
        flex-wrap: wrap;
    }

    .btn-action {
        padding: 8px 16px;
        border-radius: 10px;
        font-size: 12.5px;
        font-weight: 700;
        text-decoration: none;
        display: inline-flex;
        align-items: center;
        gap: 6px;
        cursor: pointer;
        border: 1px solid transparent;
        transition: all 0.2s;
    }

    .btn-receipt {
        background: #f8fafc;
        color: #334155;
        border-color: #cbd5e1;
    }

    .btn-receipt:hover {
        background: #e2e8f0;
        color: #0f172a;
    }

    .btn-sim-success {
        background: #059669;
        color: white;
    }

    .btn-sim-success:hover {
        background: #047857;
    }

    .btn-sim-fail {
        background: #fee2e2;
        color: #991b1b;
        border-color: #fca5a5;
    }

    .btn-sim-fail:hover {
        background: #fecaca;
    }

    /* Pagination Styling Rapi */
    .pagination-wrapper {
        margin-top: 32px;
        display: flex;
        justify-content: center;
    }

    .pagination-wrapper nav {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        background: white;
        padding: 6px 12px;
        border-radius: 12px;
        border: 1px solid #e2e8f0;
        box-shadow: 0 2px 8px rgba(0,0,0,0.03);
    }

    .pagination-wrapper nav a,
    .pagination-wrapper nav span {
        padding: 6px 12px;
        font-size: 13px;
        font-weight: 700;
        border-radius: 8px;
        text-decoration: none;
        color: #334155;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        min-width: 32px;
        height: 32px;
    }

    .pagination-wrapper nav a:hover {
        background: #f1f5f9;
        color: #059669;
    }

    .pagination-wrapper nav .active span {
        background: #059669 !important;
        color: white !important;
    }

    /* Hide the huge unstyled SVG arrows */
    .pagination-wrapper svg {
        width: 16px !important;
        height: 16px !important;
        max-width: 16px !important;
        max-height: 16px !important;
        display: inline-block !important;
    }

    .pagination-wrapper .relative.z-0 {
        display: flex !important;
        align-items: center !important;
        gap: 4px !important;
    }

    .pagination-wrapper p.text-sm.text-gray-700 {
        display: none !important;
    }
</style>

<div class="history-container">
    <div class="history-header">
        <div>
            <h1>📋 Riwayat Pesanan Saya</h1>
            <p>Daftar semua transaksi pembelian beras organik Berasera dan status pembayaran Anda</p>
        </div>
        <a href="{{ route('home') }}" class="btn-action btn-receipt">
            🌾 Belanja Lagi
        </a>
    </div>

    @forelse($penjualans as $order)
        @php
            $statusBayar = strtolower($order->pembayaran->status_pembayaran ?? 'pending');
            $statusPenjualan = strtolower($order->status_penjualan ?? 'menunggu_pembayaran');
            $isPaid = in_array($statusBayar, ['settlement', 'berhasil', 'lunas']) || in_array($statusPenjualan, ['diverifikasi', 'diproses', 'dikirim', 'selesai']);
            $isFailed = in_array($statusBayar, ['cancel', 'expire', 'deny', 'gagal']) || $statusPenjualan === 'dibatalkan';
        @endphp

        <div class="order-card">
            {{-- Header Order --}}
            <div class="order-head">
                <div class="order-meta">
                    <span class="order-id">#{{ $order->id_penjualan }}</span>
                    <span class="order-date">
                        🕒 {{ \Carbon\Carbon::parse($order->tgl_jual ?? $order->created_at)->format('d M Y, H:i') }} WIB
                    </span>
                    <span style="font-size: 11.5px; font-weight: 600; color: #475569; background: #f1f5f9; padding: 3px 8px; border-radius: 6px; text-transform: uppercase;">
                        💳 {{ str_replace('_', ' ', $order->metode_transaksi) }}
                    </span>
                </div>

                @if($isPaid)
                    <span class="badge-status badge-paid">
                        <span>●</span> Pembayaran Berhasil
                    </span>
                @elseif($isFailed)
                    <span class="badge-status badge-failed">
                        <span>●</span> Pesanan Dibatalkan
                    </span>
                @else
                    <span class="badge-status badge-pending">
                        <span>●</span> Menunggu Pembayaran
                    </span>
                @endif
            </div>

            {{-- Detail Produk --}}
            <div class="order-items">
                @foreach(($order->details ?? $order->detailPenjualans ?? []) as $detail)
                    <div class="order-item-row">
                        <div class="product-info-box">
                            <div class="product-avatar">🌾</div>
                            <div class="product-text">
                                <h4>{{ $detail->produk->nama_produk ?? 'Beras Organik Berasera' }}</h4>
                                <p>{{ $detail->jumlah }} Kg x Rp {{ number_format($detail->harga_satuan, 0, ',', '.') }}</p>
                            </div>
                        </div>
                        <div class="item-subtotal">
                            Rp {{ number_format($detail->sub_total ?? ($detail->jumlah * $detail->harga_satuan), 0, ',', '.') }}
                        </div>
                    </div>
                @endforeach
            </div>

            {{-- Footer Order (Total + Actions) --}}
            <div class="order-foot">
                <div class="total-price-box">
                    <div class="lbl">Total Transaksi</div>
                    <div class="val">Rp {{ number_format($order->total, 0, ',', '.') }}</div>
                </div>

                <div class="order-actions">
                    <a href="{{ route('orders.struk', $order->id_penjualan) }}" class="btn-action btn-receipt">
                        📄 Cetak Struk
                    </a>

                    @if(!$isPaid && !$isFailed)
                        <form method="POST" action="{{ route('orders.simulasi', $order->id_penjualan) }}" style="margin: 0;">
                            @csrf
                            <input type="hidden" name="aksi" value="berhasil">
                            <button type="submit" class="btn-action btn-sim-success">
                                ✅ Simulasi Berhasil
                            </button>
                        </form>

                        <form method="POST" action="{{ route('orders.simulasi', $order->id_penjualan) }}" style="margin: 0;">
                            @csrf
                            <input type="hidden" name="aksi" value="gagal">
                            <button type="submit" class="btn-action btn-sim-fail">
                                ❌ Simulasi Gagal
                            </button>
                        </form>
                    @endif
                </div>
            </div>
        </div>
    @empty
        <div style="background: white; border-radius: 18px; border: 1px dashed #cbd5e1; padding: 48px 24px; text-align: center;">
            <div style="font-size: 40px; margin-bottom: 12px;">🍚</div>
            <h3 style="font-size: 18px; font-weight: 700; color: #0f172a; margin-bottom: 6px;">Belum Ada Riwayat Pesanan</h3>
            <p style="font-size: 13.5px; color: #64748b; margin-bottom: 20px;">Anda belum melakukan pembelian beras organik di Berasera.</p>
            <a href="{{ route('home') }}" class="btn-action btn-sim-success" style="padding: 10px 22px;">
                Mulai Belanja Sekarang →
            </a>
        </div>
    @endforelse

    {{-- Pagination Bersih & Rapi --}}
    @if($penjualans->hasPages())
        <div class="pagination-wrapper">
            {{ $penjualans->links() }}
        </div>
    @endif
</div>
@endsection
