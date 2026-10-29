@extends('layouts.app')

@section('title', 'Profil Akun Saya — Berasera')

@section('content')
<style>
    .profile-container {
        max-width: 980px;
        margin: 40px auto;
        padding: 0 24px;
    }

    .profile-grid {
        display: grid;
        grid-template-columns: 320px 1fr;
        gap: 32px;
        align-items: flex-start;
    }

    .membership-card {
        background: linear-gradient(135deg, #064e3b 0%, #047857 100%);
        color: white;
        border-radius: 20px;
        padding: 28px 24px;
        box-shadow: 0 12px 30px rgba(5, 150, 105, 0.2);
        position: relative;
        overflow: hidden;
    }

    .membership-card::after {
        content: '';
        position: absolute;
        bottom: -40px;
        right: -40px;
        width: 140px;
        height: 140px;
        background: rgba(255, 255, 255, 0.08);
        border-radius: 50%;
    }

    .member-avatar {
        width: 64px;
        height: 64px;
        background: #fde047;
        color: #064e3b;
        font-size: 26px;
        font-weight: 800;
        border-radius: 18px;
        display: flex;
        align-items: center;
        justify-content: center;
        margin-bottom: 16px;
        box-shadow: 0 4px 14px rgba(0,0,0,0.15);
    }

    .member-badge {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        background: rgba(255, 255, 255, 0.2);
        backdrop-filter: blur(8px);
        padding: 4px 12px;
        border-radius: 20px;
        font-size: 11.5px;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        margin-bottom: 12px;
    }

    .member-name {
        font-size: 20px;
        font-weight: 800;
        line-height: 1.2;
        margin-bottom: 4px;
    }

    .member-email {
        font-size: 13px;
        color: #a7f3d0;
        margin-bottom: 24px;
    }

    .member-points-box {
        background: rgba(0, 0, 0, 0.25);
        border-radius: 14px;
        padding: 16px;
        border: 1px solid rgba(255, 255, 255, 0.1);
    }

    .points-val {
        font-size: 24px;
        font-weight: 800;
        color: #fde047;
        line-height: 1;
    }

    .points-sub {
        font-size: 11.5px;
        color: #d1fae5;
        margin-top: 4px;
    }

    /* Form Card */
    .form-card {
        background: white;
        border-radius: 20px;
        border: 1px solid #e2e8f0;
        padding: 32px;
        box-shadow: 0 4px 20px rgba(0, 0, 0, 0.03);
    }

    .form-card h2 {
        font-size: 20px;
        font-weight: 800;
        color: #0f172a;
        margin-bottom: 4px;
    }

    .form-card p {
        font-size: 13.5px;
        color: #64748b;
        margin-bottom: 24px;
    }

    .form-grid {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 16px;
        margin-bottom: 16px;
    }

    .form-group {
        margin-bottom: 18px;
    }

    .form-group label {
        display: block;
        font-size: 13px;
        font-weight: 700;
        color: #334155;
        margin-bottom: 6px;
    }

    .form-group input,
    .form-group textarea {
        width: 100%;
        padding: 10px 14px;
        border: 1.5px solid #cbd5e1;
        border-radius: 10px;
        font-family: inherit;
        font-size: 13.5px;
        color: #0f172a;
        background: #f8fafc;
        transition: all 0.2s;
    }

    .form-group input:focus,
    .form-group textarea:focus {
        background: white;
        border-color: #059669;
        outline: none;
        box-shadow: 0 0 0 3px rgba(5, 150, 105, 0.12);
    }

    .btn-save {
        background: #059669;
        color: white;
        border: none;
        padding: 12px 24px;
        border-radius: 12px;
        font-size: 13.5px;
        font-weight: 700;
        cursor: pointer;
        transition: all 0.2s;
        display: inline-flex;
        align-items: center;
        gap: 8px;
        box-shadow: 0 4px 12px rgba(5, 150, 105, 0.25);
    }

    .btn-save:hover {
        background: #047857;
        transform: translateY(-1px);
    }

    @media (max-width: 820px) {
        .profile-grid {
            grid-template-columns: 1fr;
        }
        .form-grid {
            grid-template-columns: 1fr;
        }
    }
</style>

<div class="profile-container">
    <div class="profile-grid">
        {{-- Membership Card --}}
        <div>
            <div class="membership-card">
                <div class="member-avatar">
                    {{ strtoupper(substr($customer->nama_customer, 0, 1)) }}
                </div>
                <div class="member-badge">
                    ⭐ Member {{ $customer->tier_membership ?? 'Silver' }}
                </div>
                <div class="member-name">{{ $customer->nama_customer }}</div>
                <div class="member-email">{{ $customer->email }}</div>

                <div class="member-points-box">
                    <div class="points-val">★ {{ number_format($customer->total_poin) }} Poin</div>
                    <div class="points-sub">Reward loyalitas pembelian beras organik</div>
                </div>
            </div>

            <div style="margin-top: 16px; background: white; border-radius: 14px; border: 1px solid #e2e8f0; padding: 18px; text-align: center;">
                <p style="font-size: 13px; color: #64748b; margin-bottom: 12px;">Ingin mengecek pesanan Anda?</p>
                <a href="{{ route('orders.riwayat') }}" class="btn-save" style="background: #ecfdf5; color: #047857; border: 1px solid #a7f3d0; box-shadow: none; width: 100%; justify-content: center;">
                    📋 Buka Riwayat Pesanan
                </a>
            </div>
        </div>

        {{-- Form Edit Data Diri --}}
        <div class="form-card">
            <h2>Pengaturan Profil & Alamat</h2>
            <p>Perbarui informasi akun dan alamat pengiriman beras untuk kemudahan checkout otomatis.</p>

            <form action="{{ route('customer.profile.update') }}" method="POST">
                @csrf
                @method('PUT')

                <div class="form-grid">
                    <div class="form-group">
                        <label>Nama Lengkap *</label>
                        <input type="text" name="nama_customer" value="{{ old('nama_customer', $customer->nama_customer) }}" required>
                    </div>

                    <div class="form-group">
                        <label>Username *</label>
                        <input type="text" name="username" value="{{ old('username', $customer->username) }}" required>
                    </div>
                </div>

                <div class="form-grid">
                    <div class="form-group">
                        <label>Alamat Email *</label>
                        <input type="email" name="email" value="{{ old('email', $customer->email) }}" required>
                    </div>

                    <div class="form-group">
                        <label>Nomor WhatsApp / Telepon *</label>
                        <input type="text" name="telepon" value="{{ old('telepon', $customer->telepon) }}" placeholder="Contoh: 08123456789" required>
                    </div>
                </div>

                <div class="form-group">
                    <label>Alamat Pengiriman Lengkap (Kota Metro / Sekitarnya) *</label>
                    <textarea name="alamat" rows="3" required placeholder="Tuliskan nama jalan, nomor rumah, RT/RW, kelurahan, kecamatan">{{ old('alamat', $customer->alamat) }}</textarea>
                </div>

                <div style="border-top: 1px dashed #e2e8f0; margin: 24px 0 18px 0; padding-top: 18px;">
                    <h3 style="font-size: 15px; font-weight: 700; color: #0f172a; margin-bottom: 4px;">Ganti Password (Opsional)</h3>
                    <p style="font-size: 12.5px; color: #64748b; margin-bottom: 16px;">Kosongkan jika Anda tidak ingin mengubah password akun.</p>

                    <div class="form-grid">
                        <div class="form-group" style="margin-bottom: 0;">
                            <label>Password Baru</label>
                            <input type="password" name="password" placeholder="Minimal 6 karakter">
                        </div>

                        <div class="form-group" style="margin-bottom: 0;">
                            <label>Konfirmasi Password Baru</label>
                            <input type="password" name="password_confirmation" placeholder="Ulangi password baru">
                        </div>
                    </div>
                </div>

                <div style="margin-top: 24px;">
                    <button type="submit" class="btn-save">
                        💾 Simpan Perubahan Profil
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

