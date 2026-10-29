@extends('layouts.app')

@section('title', 'Daftar Akun Pembeli - Berasera')

@section('content')
<div style="max-width: 520px; margin: 40px auto; padding: 0 20px;">
    <div style="background: white; border-radius: 20px; padding: 35px 32px; box-shadow: 0 10px 30px rgba(0,0,0,0.06); border: 1px solid #e2e8f0;">
        <div style="text-align: center; margin-bottom: 24px;">
            <div style="font-size: 38px; margin-bottom: 8px;">🌾</div>
            <h2 style="font-size: 22px; font-weight: 800; color: #064e3b;">Daftar Akun Pembeli Baru</h2>
            <p style="font-size: 13.5px; color: #64748b; margin-top: 4px;">Dapatkan beras organik berkualitas &amp; kumpulkan Poin Loyalitas</p>
        </div>

        @if($errors->any())
            <div style="background: #fee2e2; color: #991b1b; padding: 12px 16px; border-radius: 10px; font-size: 13px; margin-bottom: 20px; border: 1px solid #fecaca;">
                <ul style="margin-left: 18px;">
                    @foreach($errors->all() as $err)
                        <li>{{ $err }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form action="{{ route('register.post') }}" method="POST">
            @csrf
            <div style="margin-bottom: 16px;">
                <label style="display: block; font-size: 13px; font-weight: 700; color: #334155; margin-bottom: 6px;">Nama Lengkap</label>
                <input type="text" name="nama_customer" value="{{ old('nama_customer') }}" required
                       placeholder="Nama lengkap Anda"
                       style="width: 100%; padding: 11px 14px; border-radius: 10px; border: 1px solid #cbd5e1; font-size: 14px; font-family: inherit;">
            </div>

            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 14px; margin-bottom: 16px;">
                <div>
                    <label style="display: block; font-size: 13px; font-weight: 700; color: #334155; margin-bottom: 6px;">Email</label>
                    <input type="email" name="email" value="{{ old('email') }}" required
                           placeholder="nama@email.com"
                           style="width: 100%; padding: 11px 14px; border-radius: 10px; border: 1px solid #cbd5e1; font-size: 14px; font-family: inherit;">
                </div>
                <div>
                    <label style="display: block; font-size: 13px; font-weight: 700; color: #334155; margin-bottom: 6px;">No. WhatsApp / HP</label>
                    <input type="text" name="telepon" value="{{ old('telepon') }}" required
                           placeholder="08xxxxxxxxxx"
                           style="width: 100%; padding: 11px 14px; border-radius: 10px; border: 1px solid #cbd5e1; font-size: 14px; font-family: inherit;">
                </div>
            </div>

            <div style="margin-bottom: 16px;">
                <label style="display: block; font-size: 13px; font-weight: 700; color: #334155; margin-bottom: 6px;">Alamat Pengiriman</label>
                <textarea name="alamat" rows="2" required placeholder="Alamat lengkap tujuan kirim beras"
                          style="width: 100%; padding: 11px 14px; border-radius: 10px; border: 1px solid #cbd5e1; font-size: 14px; font-family: inherit;">{{ old('alamat') }}</textarea>
            </div>

            <div style="margin-bottom: 16px;">
                <label style="display: block; font-size: 13px; font-weight: 700; color: #334155; margin-bottom: 6px;">Username</label>
                <input type="text" name="username" value="{{ old('username') }}" required
                       placeholder="Pilih username unik"
                       style="width: 100%; padding: 11px 14px; border-radius: 10px; border: 1px solid #cbd5e1; font-size: 14px; font-family: inherit;">
            </div>

            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 14px; margin-bottom: 24px;">
                <div>
                    <label style="display: block; font-size: 13px; font-weight: 700; color: #334155; margin-bottom: 6px;">Kata Sandi</label>
                    <input type="password" name="password" required placeholder="Minimal 6 karakter"
                           style="width: 100%; padding: 11px 14px; border-radius: 10px; border: 1px solid #cbd5e1; font-size: 14px; font-family: inherit;">
                </div>
                <div>
                    <label style="display: block; font-size: 13px; font-weight: 700; color: #334155; margin-bottom: 6px;">Ulangi Sandi</label>
                    <input type="password" name="password_confirmation" required placeholder="Ketik ulang sandi"
                           style="width: 100%; padding: 11px 14px; border-radius: 10px; border: 1px solid #cbd5e1; font-size: 14px; font-family: inherit;">
                </div>
            </div>

            <button type="submit" style="width: 100%; background: #059669; color: white; border: none; padding: 13px; border-radius: 10px; font-size: 14.5px; font-weight: 700; cursor: pointer; box-shadow: 0 4px 14px rgba(5,150,105,0.35);">
                Daftar &amp; Dapatkan Poin
            </button>
        </form>

        <div style="margin-top: 22px; text-align: center; font-size: 13.5px; color: #64748b; border-top: 1px solid #f1f5f9; padding-top: 18px;">
            Sudah punya akun? <a href="{{ route('login') }}" style="color: #059669; font-weight: 700; text-decoration: none;">Masuk di sini</a>
        </div>
    </div>
</div>
@endsection
