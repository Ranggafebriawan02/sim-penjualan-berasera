@extends('layouts.app')

@section('title', 'Login Pengurus Poktan - Berasera')

@section('content')
<div style="max-width: 480px; margin: 40px auto; padding: 0 20px;">
    <div style="background: #0f172a; border-radius: 20px; padding: 35px 30px; box-shadow: 0 14px 35px rgba(0,0,0,0.3); border: 1px solid #1e293b; color: white;">
        <div style="text-align: center; margin-bottom: 24px;">
            <div style="font-size: 38px; margin-bottom: 8px;">🔐</div>
            <h2 style="font-size: 21px; font-weight: 800; color: #38bdf8;">Login Pengurus Poktan</h2>
            <p style="font-size: 13px; color: #94a3b8; margin-top: 4px;">Khusus Ketua &amp; Bendahara Gapsera Sejahtera Mandiri</p>
        </div>

        @if(session('error'))
            <div style="background: rgba(239, 68, 68, 0.2); color: #fca5a5; padding: 12px 16px; border-radius: 10px; font-size: 13px; margin-bottom: 20px; border: 1px solid rgba(239, 68, 68, 0.3);">
                {{ session('error') }}
            </div>
        @endif

        @if($errors->any())
            <div style="background: rgba(239, 68, 68, 0.2); color: #fca5a5; padding: 12px 16px; border-radius: 10px; font-size: 13px; margin-bottom: 20px; border: 1px solid rgba(239, 68, 68, 0.3);">
                {{ $errors->first() }}
            </div>
        @endif

        {{-- 1-Click Demo Login Box --}}
        <div style="background: rgba(14, 165, 233, 0.12); border: 1px solid rgba(14, 165, 233, 0.3); border-radius: 12px; padding: 14px 16px; margin-bottom: 22px;">
            <div style="font-size: 12.5px; font-weight: 700; color: #38bdf8; margin-bottom: 10px; display: flex; align-items: center; gap: 6px;">
                <span>⚡</span> KLIK INSTAN 1-DETIK (DEMO LOGIN):
            </div>
            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 8px;">
                <a href="{{ route('demo.login', 'ketua') }}" style="background: #0284c7; color: white; text-decoration: none; padding: 9px 10px; border-radius: 8px; font-size: 12.5px; font-weight: 700; text-align: center; display: block; box-shadow: 0 2px 8px rgba(2,132,199,0.3);">
                    👑 Masuk sbg Ketua
                </a>
                <a href="{{ route('demo.login', 'bendahara') }}" style="background: #0f766e; color: white; text-decoration: none; padding: 9px 10px; border-radius: 8px; font-size: 12.5px; font-weight: 700; text-align: center; display: block; box-shadow: 0 2px 8px rgba(15,118,110,0.3);">
                    💼 Masuk sbg Bendahara
                </a>
            </div>
        </div>

        <form action="{{ route('admin.login.post') }}" method="POST">
            @csrf
            <div style="margin-bottom: 16px;">
                <label style="display: block; font-size: 13px; font-weight: 600; color: #cbd5e1; margin-bottom: 6px;">Username Pengurus</label>
                <input type="text" id="admin_username" name="username" value="{{ old('username', 'ketua_gapsera') }}" required autofocus
                       placeholder="ketua_gapsera / bendahara_gapsera / admin"
                       style="width: 100%; padding: 12px 14px; border-radius: 10px; border: 1px solid #334155; background: #1e293b; color: white; font-size: 14px; font-family: inherit;">
            </div>

            <div style="margin-bottom: 20px;">
                <label style="display: block; font-size: 13px; font-weight: 600; color: #cbd5e1; margin-bottom: 6px;">Kata Sandi</label>
                <input type="password" id="admin_password" name="password" value="admin123" required
                       placeholder="Masukkan kata sandi"
                       style="width: 100%; padding: 12px 14px; border-radius: 10px; border: 1px solid #334155; background: #1e293b; color: white; font-size: 14px; font-family: inherit;">
            </div>

            <button type="submit" style="width: 100%; background: linear-gradient(135deg, #0284c7, #0369a1); color: white; border: none; padding: 13px; border-radius: 10px; font-size: 14.5px; font-weight: 700; cursor: pointer; box-shadow: 0 4px 14px rgba(2,132,199,0.4);">
                Masuk ke Dashboard Pengurus &rarr;
            </button>
        </form>

        <div style="margin-top: 22px; text-align: center; font-size: 12px; color: #94a3b8; border-top: 1px solid #1e293b; padding-top: 16px;">
            <p style="margin-bottom: 6px;">Klik cepat untuk isi form:</p>
            <div style="display: flex; gap: 8px; justify-content: center; flex-wrap: wrap;">
                <button type="button" onclick="fillAdmin('ketua_gapsera', 'admin123')" style="background: #1e293b; border: 1px solid #334155; color: #38bdf8; padding: 5px 10px; border-radius: 6px; font-size: 11.5px; cursor: pointer;">
                    Ketua: ketua_gapsera
                </button>
                <button type="button" onclick="fillAdmin('bendahara_gapsera', 'admin123')" style="background: #1e293b; border: 1px solid #334155; color: #2dd4bf; padding: 5px 10px; border-radius: 6px; font-size: 11.5px; cursor: pointer;">
                    Bendahara: bendahara_gapsera
                </button>
                <!-- <button type="button" onclick="fillAdmin('admin', 'admin123')" style="background: #1e293b; border: 1px solid #334155; color: #f59e0b; padding: 5px 10px; border-radius: 6px; font-size: 11.5px; cursor: pointer;">
                    Admin: admin
                </button> -->
            </div>
            <div style="margin-top: 16px;">
                <a href="{{ route('login') }}" style="color: #64748b; text-decoration: none; font-size: 12px;">&larr; Masuk sebagai Akun Pembeli (Customer)</a>
            </div>
        </div>
    </div>
    
</div>

<script>
function fillAdmin(user, pass) {

    document.getElementById('admin_username').value = user;
    document.getElementById('admin_password').value = pass;
}
</script>
@endsection
