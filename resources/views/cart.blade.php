@extends('layouts.app')

@section('title', 'Keranjang Belanja Beras - Berasera')

@section('content')
<div style="max-width: 1050px; margin: 40px auto; padding: 0 24px;">
    <h2 style="font-size: 24px; font-weight: 800; color: #0f172a; margin-bottom: 24px;">
        🛒 Keranjang Belanja Beras Organik
    </h2>

    @if(session('success'))
        <div style="background: #ecfdf5; color: #065f46; border: 1px solid #a7f3d0; padding: 12px 18px; border-radius: 12px; margin-bottom: 20px; font-size: 13.5px; display: flex; align-items: center; gap: 8px;">
            <span>✅</span> {{ session('success') }}
        </div>
    @endif

    @if(session('error'))
        <div style="background: #fee2e2; color: #991b1b; border: 1px solid #fecaca; padding: 12px 18px; border-radius: 12px; margin-bottom: 20px; font-size: 13.5px;">
            ⚠️ {{ session('error') }}
        </div>
    @endif

    @if(count($cart) > 0)
        <div style="display: grid; grid-template-columns: 1.6fr 1.1fr; gap: 32px; align-items: start;">
            <!-- Daftar Produk Beras -->
            <div style="background: white; border-radius: 18px; border: 1px solid #e2e8f0; overflow: hidden; box-shadow: 0 4px 16px rgba(0,0,0,0.03);">
                <table style="width: 100%; border-collapse: collapse; font-size: 13.5px;">
                    <thead style="background: #f8fafc; border-bottom: 1px solid #e2e8f0; text-align: left; color: #475569;">
                        <tr>
                            <th style="padding: 14px 18px;">Produk Beras</th>
                            <th style="padding: 14px 18px;">Harga</th>
                            <th style="padding: 14px 18px; text-align: center;">Jumlah</th>
                            <th style="padding: 14px 18px; text-align: right;">Subtotal</th>
                            <th style="padding: 14px 14px; text-align: center;"></th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($cart as $id => $item)
                            <tr style="border-bottom: 1px solid #f1f5f9;">
                                <td style="padding: 16px 18px;">
                                    <div style="font-weight: 700; color: #0f172a; font-size: 14px;">{{ $item['nama'] }}</div>
                                    <div style="font-size: 12px; color: #059669; font-weight: 600; margin-top: 2px;">{{ $item['kualitas'] }}</div>
                                </td>
                                <td style="padding: 16px 18px; color: #475569; font-weight: 500;">
                                    Rp {{ number_format($item['harga'], 0, ',', '.') }}
                                </td>
                                <td style="padding: 16px 18px;">
                                    <!-- Tombol + dan - Cepat (Tanpa tombol update) -->
                                    <div style="display: flex; align-items: center; justify-content: center; gap: 8px;">
                                        <a href="{{ route('cart.decrease', $id) }}" 
                                           style="width: 30px; height: 30px; display: inline-flex; align-items: center; justify-content: center; background: #f1f5f9; border: 1px solid #cbd5e1; border-radius: 8px; text-decoration: none; color: #334155; font-weight: 800; font-size: 16px; transition: all 0.2s;"
                                           title="Kurangi 1">−</a>
                                        
                                        <span style="min-width: 28px; text-align: center; font-weight: 800; font-size: 14.5px; color: #0f172a;">
                                            {{ $item['qty'] }}
                                        </span>
                                        
                                        <a href="{{ route('cart.increase', $id) }}" 
                                           style="width: 30px; height: 30px; display: inline-flex; align-items: center; justify-content: center; background: #ecfdf5; border: 1px solid #a7f3d0; border-radius: 8px; text-decoration: none; color: #059669; font-weight: 800; font-size: 16px; transition: all 0.2s;"
                                           title="Tambah 1">+</a>
                                    </div>
                                </td>
                                <td style="padding: 16px 18px; font-weight: 800; color: #064e3b; text-align: right; font-size: 14.5px;">
                                    Rp {{ number_format($item['harga'] * $item['qty'], 0, ',', '.') }}
                                </td>
                                <td style="padding: 16px 14px; text-align: center;">
                                    <a href="{{ route('cart.remove', $id) }}" 
                                       onclick="return confirm('Hapus beras ini dari keranjang?')"
                                       style="color: #ef4444; text-decoration: none; font-size: 16px; font-weight: bold; padding: 4px 8px;"
                                       title="Hapus Produk">✕</a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <!-- Ringkasan & Form Checkout -->
            <div style="background: white; border-radius: 18px; border: 1px solid #e2e8f0; padding: 26px; box-shadow: 0 4px 16px rgba(0,0,0,0.03);">
                <h3 style="font-size: 17px; font-weight: 800; color: #0f172a; margin-bottom: 18px;">Ringkasan Pembayaran</h3>

                <form action="{{ route('checkout.process') }}" method="POST">
                    @csrf

                    <!-- Pilihan Metode Pembayaran -->
                    <div style="margin-bottom: 22px;">
                        <label style="display: block; font-size: 13px; font-weight: 700; color: #334155; margin-bottom: 8px;">Metode Pembayaran</label>
                        <div style="display: flex; flex-direction: column; gap: 10px; font-size: 13px;">
                            <label style="display: flex; align-items: center; gap: 10px; padding: 12px 14px; border: 2px solid #059669; border-radius: 10px; cursor: pointer; background: #f0fdf4;">
                                <input type="radio" name="metode_transaksi" value="midtrans_qris" checked style="accent-color: #059669;">
                                <div>
                                    <div style="font-weight: 700; color: #065f46;">💳 Midtrans Snap (QRIS, GoPay, ShopeePay &amp; Virtual Account)</div>
                                    <div style="font-size: 11.5px; color: #047857; margin-top: 1px;">Pembayaran digital instan aman &amp; otomatis terverifikasi</div>
                                </div>
                            </label>

                            <label style="display: flex; align-items: center; gap: 10px; padding: 12px 14px; border: 1px solid #e2e8f0; border-radius: 10px; cursor: pointer;">
                                <input type="radio" name="metode_transaksi" value="cash" style="accent-color: #059669;">
                                <div>
                                    <div style="font-weight: 700; color: #1e293b;">💵 Tunai / Cash</div>
                                    <div style="font-size: 11.5px; color: #64748b; margin-top: 1px;">Bayar langsung di kasir gudang Poktan Gapsera</div>
                                </div>
                            </label>
                        </div>
                    </div>

                    <!-- Info Poin Loyalitas Pelanggan -->
                    @if(Auth::guard('customer')->check())
                        <div style="background: #fffbeb; border: 1px dashed #f59e0b; padding: 12px 14px; border-radius: 10px; margin-bottom: 20px; font-size: 12.5px;">
                            <div style="font-weight: 700; color: #92400e; display: flex; align-items: center; gap: 6px;">
                                <span>🎁</span> Saldo Anda: {{ Auth::guard('customer')->user()->total_poin }} Poin
                            </div>
                            <div style="color: #78350f; margin-top: 3px;">
                                Pembelian ini akan menambah <strong>+{{ floor($totalHarga / 10000) }} Poin</strong> loyalitas ke akun Anda!
                            </div>
                        </div>
                    @endif

                    <!-- Rincian Total -->
                    <div style="border-top: 1px solid #e2e8f0; padding-top: 16px; margin-bottom: 22px;">
                        <div style="display: flex; justify-content: space-between; font-size: 14px; margin-bottom: 10px; color: #475569;">
                            <span>Subtotal Belanja:</span>
                            <span style="font-weight: 600;">Rp {{ number_format($totalHarga, 0, ',', '.') }}</span>
                        </div>
                        <div style="display: flex; justify-content: space-between; font-size: 18.5px; font-weight: 800; color: #064e3b; padding-top: 12px; border-top: 1px dashed #e2e8f0;">
                            <span>Total Tagihan:</span>
                            <span>Rp {{ number_format($totalHarga, 0, ',', '.') }}</span>
                        </div>
                    </div>

                    <!-- Tombol Bayar Sekarang Aktif -->
                    @if(Auth::guard('customer')->check())
                        <button type="submit" 
                                style="width: 100%; background: #059669; color: white; border: none; padding: 15px; border-radius: 12px; font-size: 15px; font-weight: 800; cursor: pointer; box-shadow: 0 4px 14px rgba(5,150,105,0.4); display: flex; align-items: center; justify-content: center; gap: 8px;">
                            <span>Bayar Sekarang</span>
                            <span>&rarr;</span>
                        </button>
                    @else
                        <a href="{{ route('demo.login', 'budi') }}" 
                           style="display: block; text-align: center; width: 100%; background: #059669; color: white; text-decoration: none; padding: 14px; border-radius: 12px; font-size: 14.5px; font-weight: 800; box-shadow: 0 4px 14px rgba(5,150,105,0.4);">
                            ⚡ Masuk sebagai Pembeli untuk Bayar
                        </a>
                        <div style="text-align: center; margin-top: 8px;">
                            <a href="{{ route('login') }}" style="font-size: 12px; color: #64748b; text-decoration: none;">Atau login manual &rarr;</a>
                        </div>
                    @endif
                </form>
            </div>
        </div>
    @else
        <div style="background: white; border-radius: 18px; padding: 60px 24px; text-align: center; border: 1px solid #e2e8f0;">
            <div style="font-size: 54px; margin-bottom: 12px;">🌾</div>
            <h3 style="font-size: 20px; font-weight: 700; color: #1e293b; margin-bottom: 8px;">Keranjang Belanja Masih Kosong</h3>
            <p style="font-size: 14px; color: #64748b; margin-bottom: 24px;">Silakan pilih beras organik segar Berasera di katalog kami.</p>
            <a href="{{ route('home') }}" class="btn-buy" style="text-decoration: none; padding: 12px 24px; border-radius: 10px;">
                Jelajahi Katalog Beras
            </a>
        </div>
    @endif
</div>
@endsection
