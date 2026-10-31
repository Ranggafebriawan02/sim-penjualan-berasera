@extends('layouts.app')

@section('title', 'Pembayaran Faktur ' . $penjualan->id_penjualan . ' - Berasera')

@section('content')
<div style="max-width: 860px; margin: 40px auto; padding: 0 20px;">
    @if(session('success'))
        <div style="background: #ecfdf5; color: #065f46; border: 1px solid #a7f3d0; padding: 14px 20px; border-radius: 14px; margin-bottom: 24px; font-size: 14px; display: flex; align-items: center; gap: 10px; box-shadow: 0 4px 12px rgba(6,95,70,0.06);">
            <span style="font-size: 20px;">🎉</span>
            <div>
                <strong>Berhasil!</strong> {{ session('success') }}
            </div>
        </div>
    @endif

    @if(session('error'))
        <div style="background: #fee2e2; color: #991b1b; border: 1px solid #fecaca; padding: 14px 20px; border-radius: 14px; margin-bottom: 24px; font-size: 14px;">
            ⚠️ {{ session('error') }}
        </div>
    @endif

    <div style="background: white; border-radius: 20px; border: 1px solid #e2e8f0; overflow: hidden; box-shadow: 0 8px 30px rgba(0,0,0,0.05);">
        {{-- Header Nota --}}
        <div style="background: #064e3b; color: white; padding: 24px 30px; display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 16px;">
            <div>
                <div style="font-size: 12px; text-transform: uppercase; letter-spacing: 1.5px; opacity: 0.8; font-weight: 700;">FAKTUR PENJUALAN BERASERA</div>
                <h2 style="font-size: 22px; font-weight: 800; margin-top: 2px;">{{ $penjualan->id_penjualan }}</h2>
                <div style="font-size: 13px; opacity: 0.85; margin-top: 4px;">
                    Tanggal: {{ date('d M Y', strtotime($penjualan->tgl_jual)) }} &bull; Pembeli: {{ $penjualan->customer->nama_customer }}
                </div>
            </div>

            <div>
                @if($penjualan->pembayaran && $penjualan->pembayaran->status_pembayaran === 'settlement')
                    <span style="background: #10b981; color: white; padding: 8px 16px; border-radius: 20px; font-size: 13px; font-weight: 800; display: inline-flex; align-items: center; gap: 6px; box-shadow: 0 2px 10px rgba(16,185,129,0.3);">
                        ✓ PEMBAYARAN LUNAS
                    </span>
                @else
                    <span style="background: #f59e0b; color: #78350f; padding: 8px 16px; border-radius: 20px; font-size: 13px; font-weight: 800; display: inline-flex; align-items: center; gap: 6px;">
                        ⏳ MENUNGGU PEMBAYARAN
                    </span>
                @endif
            </div>
        </div>

        <div style="padding: 30px;">
            {{-- Bagian Pembayaran Midtrans Snap & QRIS --}}
            @if($penjualan->metode_transaksi !== 'cash')
                <div style="background: #f8fafc; border: 1.5px solid #e2e8f0; border-radius: 16px; padding: 28px; margin-bottom: 30px; text-align: center;">
                    <div style="display: inline-block; background: #0284c7; color: white; font-size: 12px; font-weight: 800; padding: 4px 14px; border-radius: 20px; margin-bottom: 12px; letter-spacing: 0.5px;">
                        💳 MIDTRANS SNAP PAYMENT GATEWAY (SANDBOX)
                    </div>
                    <h3 style="font-size: 20px; font-weight: 800; color: #0f172a; margin-bottom: 8px;">
                        Bayar Online Instan &amp; Terverifikasi Otomatis
                    </h3>
                    <p style="font-size: 13.5px; color: #64748b; max-width: 520px; margin: 0 auto 24px;">
                        Klik tombol di bawah untuk membuka pop-up pembayaran resmi Midtrans Snap. Tersedia metode <strong>QRIS (Gopay / ShopeePay / DANA)</strong>, <strong>Virtual Account BCA / BNI / BRI / Mandiri</strong>, dan lainnya.
                    </p>

                    {{-- Detail Total Tagihan --}}
                    <div style="background: white; border-radius: 14px; padding: 18px 24px; max-width: 440px; margin: 0 auto 24px; border: 1px solid #cbd5e1; text-align: left; box-shadow: 0 2px 10px rgba(0,0,0,0.02);">
                        <div style="display: flex; justify-content: space-between; font-size: 13px; color: #64748b; margin-bottom: 6px;">
                            <span>No. Faktur:</span>
                            <strong style="color: #0f172a; font-family: monospace;">{{ $penjualan->id_penjualan }}</strong>
                        </div>
                        <div style="display: flex; justify-content: space-between; font-size: 13px; color: #64748b; margin-bottom: 10px;">
                            <span>Pelanggan:</span>
                            <strong style="color: #0f172a;">{{ $penjualan->customer->nama_customer }}</strong>
                        </div>
                        <div style="border-top: 1px dashed #e2e8f0; padding-top: 10px; display: flex; justify-content: space-between; align-items: center;">
                            <div>
                                <div style="font-size: 12px; color: #64748b;">Total Tagihan:</div>
                                <div style="font-size: 24px; font-weight: 800; color: #059669;">
                                    Rp {{ number_format($penjualan->total, 0, ',', '.') }}
                                </div>
                            </div>
                            <span style="font-size: 11.5px; background: #ecfdf5; color: #047857; font-weight: 700; padding: 4px 10px; border-radius: 20px; border: 1px solid #a7f3d0;">
                                Bebas Biaya Layanan
                            </span>
                        </div>
                    </div>

                    {{-- Tombol Utama: Buka Midtrans Snap Popup --}}
                    @if(!$penjualan->pembayaran || $penjualan->pembayaran->status_pembayaran !== 'settlement')
                        @if(!empty($penjualan->pembayaran->snap_token))
                            <div style="margin-bottom: 16px;">
                                <button type="button" id="pay-button" 
                                        style="background: linear-gradient(135deg, #0284c7, #0369a1); color: white; border: none; padding: 16px 36px; border-radius: 14px; font-size: 16px; font-weight: 800; cursor: pointer; box-shadow: 0 4px 18px rgba(2,132,199,0.4); display: inline-flex; align-items: center; gap: 10px; transition: transform 0.15s ease;">
                                    <span>⚡ Bayar Sekarang (Buka Midtrans Snap)</span>
                                    <span>&rarr;</span>
                                </button>
                            </div>
                            <p style="font-size: 12.5px; color: #64748b; margin-bottom: 20px;">
                                Popup Midtrans akan menampilkan barcode QRIS atau instruksi Virtual Account bank pilihan Anda.
                            </p>
                        @else
                            <div style="background: #fffbeb; border: 1px solid #fef3c7; color: #92400e; padding: 12px 18px; border-radius: 12px; max-width: 440px; margin: 0 auto 20px; font-size: 13px;">
                                ⚠️ Snap Token belum terbentuk. Pastikan kredensial Midtrans di <code>.env</code> sudah benar lalu refresh halaman ini.
                            </div>
                        @endif
                    @else
                        <div style="background: #ecfdf5; border: 1px solid #a7f3d0; border-radius: 14px; padding: 18px; max-width: 440px; margin: 0 auto 20px; color: #065f46;">
                            <div style="font-size: 16px; font-weight: 800;">✓ Pembayaran Berhasil Terverifikasi!</div>
                            <div style="font-size: 13px; margin-top: 4px;">
                                Waktu Bayar: {{ $penjualan->pembayaran->waktu_bayar ? date('d M Y H:i', strtotime($penjualan->pembayaran->waktu_bayar)) : '-' }} WIB
                            </div>
                        </div>
                    @endif

                    {{-- Manual Fallback Form (Opsional) --}}
                    @if(!$penjualan->pembayaran || $penjualan->pembayaran->status_pembayaran !== 'settlement')
                        <div style="margin-top: 24px; padding-top: 20px; border-top: 1px solid #e2e8f0;">
                            <button type="button" onclick="document.getElementById('manualFallbackBox').style.display = document.getElementById('manualFallbackBox').style.display === 'none' ? 'block' : 'none'"
                                    style="background: transparent; border: none; color: #64748b; font-size: 12px; font-weight: 600; cursor: pointer; text-decoration: underline;">
                                Ingin konfirmasi manual dengan bukti transfer? Klik di sini
                            </button>
                            <div id="manualFallbackBox" style="display: none; max-width: 440px; margin: 16px auto 0; text-align: left; background: white; padding: 16px; border-radius: 12px; border: 1px solid #e2e8f0;">
                                <form action="{{ route('payment.confirm', $penjualan->id_penjualan) }}" method="POST" enctype="multipart/form-data">
                                    @csrf
                                    <label style="display: block; font-size: 12px; font-weight: 600; color: #475569; margin-bottom: 6px;">
                                        Upload Bukti Transfer / Screenshot:
                                    </label>
                                    <input type="file" name="bukti_transfer" accept="image/*" 
                                           style="width: 100%; font-size: 12px; padding: 8px; border: 1px solid #cbd5e1; border-radius: 8px; margin-bottom: 10px;">
                                    <button type="submit" style="background: #059669; color: white; border: none; padding: 10px 18px; border-radius: 8px; font-size: 13px; font-weight: 700; cursor: pointer; width: 100%;">
                                        Konfirmasi Manual
                                    </button>
                                </form>
                            </div>
                        </div>
                    @endif
                </div>
            @else
                {{-- Pembayaran Tunai --}}
                <div style="background: #f8fafc; border: 1.5px solid #e2e8f0; border-radius: 16px; padding: 24px; margin-bottom: 30px; text-align: center;">
                    <div style="font-size: 32px; margin-bottom: 8px;">💵</div>
                    <h3 style="font-size: 18px; font-weight: 800; color: #0f172a; margin-bottom: 6px;">Metode Pembayaran Tunai (Cash)</h3>
                    <p style="font-size: 13.5px; color: #64748b; max-width: 480px; margin: 0 auto 16px;">
                        Silakan tunjukkan nomor faktur <strong>{{ $penjualan->id_penjualan }}</strong> kepada kasir / bendahara di gudang Poktan Gapsera Sejahtera Mandiri saat pengambilan beras.
                    </p>
                    <div style="font-size: 22px; font-weight: 800; color: #059669;">
                        Total Tagihan: Rp {{ number_format($penjualan->total, 0, ',', '.') }}
                    </div>
                </div>
            @endif

            {{-- Rincian Produk & Transparansi FIFO --}}
            <h4 style="font-size: 16px; font-weight: 800; color: #0f172a; margin-bottom: 14px;">Rincian Pesanan</h4>
            <table style="width: 100%; border-collapse: collapse; font-size: 13.5px; margin-bottom: 24px;">
                <thead style="background: #f8fafc; border-bottom: 2px solid #e2e8f0; text-align: left; color: #475569;">
                    <tr>
                        <th style="padding: 12px 14px;">Item Beras</th>
                        <th style="padding: 12px 14px;">Harga</th>
                        <th style="padding: 12px 14px; text-align: center;">Jumlah</th>
                        <th style="padding: 12px 14px; text-align: right;">Subtotal</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($penjualan->details as $det)
                        <tr style="border-bottom: 1px solid #f1f5f9;">
                            <td style="padding: 14px;">
                                <div style="font-weight: 700; color: #0f172a;">{{ $det->produk->nama_produk }}</div>
                                <div style="font-size: 12px; color: #059669;">{{ $det->produk->kualitas->nama_kualitas ?? 'Grade Organik' }}</div>
                                
                                {{-- Audit Trail FIFO: Bukti Pemotongan Batch Tertua --}}
                                @if($det->alokasiBatches->count() > 0)
                                    <div style="margin-top: 6px; font-size: 11px; background: #f0fdf4; border: 1px solid #bbf7d0; border-radius: 6px; padding: 4px 8px; color: #166534; display: inline-block;">
                                        🌾 <strong>Alokasi FIFO:</strong> 
                                        @foreach($det->alokasiBatches as $alokasi)
                                            Batch {{ $alokasi->batch->kode_batch ?? 'BATCH-'.$alokasi->id_batch }} 
                                            ({{ $alokasi->jumlah_keluar }} Kg @ HPP Rp {{ number_format($alokasi->hpp_satuan, 0, ',', '.') }})
                                        @endforeach
                                    </div>
                                @endif
                            </td>
                            <td style="padding: 14px; color: #475569;">
                                Rp {{ number_format($det->harga_satuan, 0, ',', '.') }}
                            </td>
                            <td style="padding: 14px; text-align: center; font-weight: 700;">
                                {{ $det->jumlah }} {{ $det->produk->satuan }}
                            </td>
                            <td style="padding: 14px; text-align: right; font-weight: 800; color: #064e3b;">
                                Rp {{ number_format($det->sub_total, 0, ',', '.') }}
                            </td>
                        </tr>
                    @endforeach
                </tbody>
                <tfoot>
                    <tr style="font-weight: 800; font-size: 16px;">
                        <td colspan="3" style="padding: 16px 14px; text-align: right; color: #0f172a;">Total Tagihan:</td>
                        <td style="padding: 16px 14px; text-align: right; color: #059669; font-size: 18px;">
                            Rp {{ number_format($penjualan->total, 0, ',', '.') }}
                        </td>
                    </tr>
                </tfoot>
            </table>

            {{-- Tombol Navigasi --}}
            <div style="display: flex; gap: 12px; justify-content: space-between; flex-wrap: wrap; border-top: 1px solid #e2e8f0; padding-top: 20px;">
                <a href="{{ route('home') }}" style="color: #64748b; text-decoration: none; font-size: 13.5px; font-weight: 600; display: inline-flex; align-items: center; gap: 6px;">
                    &larr; Kembali ke Katalog Beras
                </a>

                <button type="button" onclick="window.print()" 
                        style="background: #f1f5f9; border: 1px solid #cbd5e1; color: #334155; padding: 10px 18px; border-radius: 8px; font-size: 13px; font-weight: 700; cursor: pointer;">
                    🖨️ Cetak Nota Struk
                </button>
            </div>
        </div>
    </div>
</div>

{{-- Midtrans Snap JS Script --}}
@if(!empty($penjualan->pembayaran->snap_token) && $penjualan->pembayaran->status_pembayaran !== 'settlement')
    @php
        $snapUrl = !empty($isProduction) 
            ? 'https://app.midtrans.com/snap/snap.js' 
            : 'https://app.sandbox.midtrans.com/snap/snap.js';
    @endphp
    <script src="{{ $snapUrl }}" data-client-key="{{ $clientKey }}"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            var payButton = document.getElementById('pay-button');
            if (payButton) {
                payButton.addEventListener('click', function () {
                    window.snap.pay('{{ $penjualan->pembayaran->snap_token }}', {
                        onSuccess: function(result){
                            console.log('Payment success:', result);
                            if (typeof bunyikanSuaraPembayaran === 'function') {
                                // Tunggu sampai suara pembacaan kalimat selesai penuh, baru reload halaman
                                bunyikanSuaraPembayaran({{ $penjualan->total }}, 'customer', function() {
                                    window.location.reload();
                                });
                            } else {
                                window.location.reload();
                            }
                        },
                        onPending: function(result){
                            console.log('Payment pending:', result);
                            window.location.reload();
                        },
                        onError: function(result){
                            console.error('Payment error:', result);
                            alert('Pembayaran gagal atau terjadi kesalahan. Silakan coba kembali.');
                        },
                        onClose: function(){
                            console.log('Customer closed popup without finishing payment');
                        }
                    });
                });
            }
        });
    </script>
@endif

<script>
function salinNominal(nominal) {
    navigator.clipboard.writeText(nominal).then(function() {
        alert('Nominal Rp ' + parseInt(nominal).toLocaleString('id-ID') + ' berhasil disalin ke clipboard!');
    });
}

function toggleCustomQrForm() {
    var box = document.getElementById('customQrBox');
    if (box.style.display === 'none' || box.style.display === '') {
        box.style.display = 'block';
    } else {
        box.style.display = 'none';
    }
}
</script>
@endsection
