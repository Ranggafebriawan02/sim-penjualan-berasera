@extends('layouts.app')

@section('title', 'Masuk Pembeli - Berasera')

@section('content')
<div style="max-width: 460px; margin: 40px auto; padding: 0 20px;">
    <div style="background: white; border-radius: 20px; padding: 35px 30px; box-shadow: 0 10px 30px rgba(0,0,0,0.06); border: 1px solid #e2e8f0;">
        <div style="text-align: center; margin-bottom: 24px;">
            <div style="font-size: 38px; margin-bottom: 8px;">🌾</div>
            <h2 style="font-size: 22px; font-weight: 800; color: #064e3b;">Masuk Akun Pembeli</h2>
            <p style="font-size: 13.5px; color: #64748b; margin-top: 4px;">Nikmati belanja beras organik &amp; kumpulkan poin loyalitas</p>
        </div>

        @if(session('info'))
            <div style="background: #e0f2fe; color: #0369a1; padding: 12px 16px; border-radius: 10px; font-size: 13px; margin-bottom: 20px; border: 1px solid #bae6fd;">
                {{ session('info') }}
            </div>
        @endif

        @if(session('error'))
            <div style="background: #fee2e2; color: #991b1b; padding: 12px 16px; border-radius: 10px; font-size: 13px; margin-bottom: 20px; border: 1px solid #fecaca;">
                {{ session('error') }}
            </div>
        @endif

        @if($errors->any())
            <div style="background: #fee2e2; color: #991b1b; padding: 12px 16px; border-radius: 10px; font-size: 13px; margin-bottom: 20px; border: 1px solid #fecaca;">
                {{ $errors->first() }}
            </div>
        @endif

        {{-- 1-Click Demo Login Box --}}
        <div style="background: #ecfdf5; border: 1px solid #a7f3d0; border-radius: 12px; padding: 14px 16px; margin-bottom: 22px;">
            <!-- <div style="font-size: 12.5px; font-weight: 700; color: #047857; margin-bottom: 8px; display: flex; align-items: center; gap: 6px;">
                <span>⚡</span> KLIK INSTAN 1-DETIK (DEMO LOGIN):
            </div> -->
            <!-- <a href="{{ route('demo.login', 'budi') }}" style="background: #059669; color: white; text-decoration: none; padding: 10px; border-radius: 8px; font-size: 13px; font-weight: 700; text-align: center; display: block; box-shadow: 0 2px 8px rgba(5,150,105,0.3);">
                🛒 Masuk Langsung sbg Pembeli (Budi Santoso)
            </a> -->
        </div>

        <form action="{{ route('login.post') }}" method="POST">
            @csrf
            <div style="margin-bottom: 16px;">
                <label style="display: block; font-size: 13px; font-weight: 700; color: #334155; margin-bottom: 6px;">Username atau Email</label>
                <input type="text" id="cust_username" name="username" value="{{ old('username', 'budi') }}" required autofocus
                       placeholder="budi atau budi@gmail.com"
                       style="width: 100%; padding: 12px 14px; border-radius: 10px; border: 1px solid #cbd5e1; font-size: 14px; font-family: inherit;">
            </div>

            <div style="margin-bottom: 20px;">
                <label style="display: block; font-size: 13px; font-weight: 700; color: #334155; margin-bottom: 6px;">Kata Sandi (Password)</label>
                <input type="password" id="cust_password" name="password" value="admin123" required
                       placeholder="Masukkan kata sandi"
                       style="width: 100%; padding: 12px 14px; border-radius: 10px; border: 1px solid #cbd5e1; font-size: 14px; font-family: inherit;">
            </div>

            <button type="submit" style="width: 100%; background: #059669; color: white; border: none; padding: 13px; border-radius: 10px; font-size: 14.5px; font-weight: 700; cursor: pointer; box-shadow: 0 4px 14px rgba(5,150,105,0.35);">
                Masuk Sekarang &rarr;
            </button>
        </form>

        <div style="margin-top: 24px; text-align: center; font-size: 13.5px; color: #64748b; border-top: 1px solid #f1f5f9; padding-top: 18px;">
            Belum memiliki akun? <a href="{{ route('register') }}" style="color: #059669; font-weight: 700; text-decoration: none;">Daftar Akun Baru</a>
        </div>
        <div style="margin-top: 12px; text-align: center; background: #f8fafc; padding: 10px; border-radius: 8px;">
            <a href="{{ route('admin.login') }}" style="font-size: 12.5px; color: #0284c7; font-weight: 600; text-decoration: none;">
                🔐 Menuju Halaman Login Pengurus (Ketua &amp; Bendahara) &rarr;
            </a>
        </div>
    </div>
</div>
@endsection
