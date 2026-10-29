@extends('layouts.admin')
@section('title', 'Data Pembeli')
@section('page-title', 'Data Pembeli / Customer')
@section('page-subtitle', 'Kelola akun pembeli yang terdaftar di sistem')

@section('content')
<div class="card">
    <div class="card-header">
        <h3>🧑‍🤝‍🧑 Daftar Pembeli Terdaftar</h3>
        <span style="font-size:13px; color:#64748b;">{{ $customers->total() }} pembeli</span>
    </div>
    <div class="table-wrapper">
        <table>
            <thead>
                <tr><th>#</th><th>Nama Pembeli</th><th>Username</th><th>Email</th><th>Telepon</th><th>Poin</th><th>Membership</th><th>Total Order</th><th>Aksi</th></tr>
            </thead>
            <tbody>
                @forelse($customers as $i => $c)
                <tr>
                    <td style="color:#94a3b8;">{{ $customers->firstItem() + $i }}</td>
                    <td style="font-weight:700; font-size:13.5px;">{{ $c->nama_customer }}</td>
                    <td style="font-size:12.5px; font-family:monospace; color:#64748b;">{{ $c->username }}</td>
                    <td style="font-size:12.5px;">{{ $c->email ?? '—' }}</td>
                    <td style="font-size:12.5px;">{{ $c->telepon ?? '—' }}</td>
                    <td><span style="font-weight:700; color:#d97706;">★ {{ number_format($c->total_poin) }}</span></td>
                    <td>
                        @php
                            $tc = ['Silver'=>'badge-info','Gold'=>'badge-warning','Platinum'=>'badge-success'];
                        @endphp
                        <span class="badge {{ $tc[$c->tier_membership] ?? 'badge-info' }}">{{ $c->tier_membership }}</span>
                    </td>
                    <td style="font-weight:700;">{{ $c->penjualans_count }}</td>
                    <td>
                        <form action="{{ route('admin.users.customer.destroy', $c->id_customer) }}" method="POST" onsubmit="return confirm('Hapus akun pembeli ini?')">
                            @csrf @method('DELETE')
                            <button class="btn btn-sm btn-danger">🗑</button>
                        </form>
                    </td>
                </tr>
                @empty
                <tr><td colspan="9" style="text-align:center; color:#94a3b8; padding:32px;">Belum ada pembeli terdaftar.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if($customers->hasPages())
    <div style="padding: 16px 22px;">{{ $customers->links() }}</div>
    @endif
</div>
@endsection
