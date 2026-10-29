<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Customer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    // ========== Manajemen Pengurus (tb_user) ==========
    public function indexPengurus()
    {
        $users = User::orderBy('nama')->paginate(20);
        return view('admin.users.pengurus', compact('users'));
    }

    public function createPengurus()
    {
        return view('admin.users.create-pengurus');
    }

    public function storePengurus(Request $request)
    {
        $data = $request->validate([
            'nama'     => 'required|string|max:100',
            'username' => 'required|string|max:50|unique:tb_user,username',
            'password' => 'required|string|min:6|confirmed',
            'level'    => 'required|in:ketua,bendahara',
        ]);

        User::create([
            'nama'     => $data['nama'],
            'username' => $data['username'],
            'password' => Hash::make($data['password']),
            'level'    => $data['level'],
        ]);

        return redirect()->route('admin.users.pengurus')->with('success', 'Akun pengurus berhasil ditambahkan!');
    }

    public function editPengurus($id)
    {
        $user = User::findOrFail($id);
        return view('admin.users.edit-pengurus', compact('user'));
    }

    public function updatePengurus(Request $request, $id)
    {
        $user = User::findOrFail($id);

        $data = $request->validate([
            'nama'     => 'required|string|max:100',
            'username' => 'required|string|max:50|unique:tb_user,username,' . $id . ',id_user',
            'password' => 'nullable|string|min:6|confirmed',
            'level'    => 'required|in:ketua,bendahara',
        ]);

        $updateData = [
            'nama'     => $data['nama'],
            'username' => $data['username'],
            'level'    => $data['level'],
        ];

        if (!empty($data['password'])) {
            $updateData['password'] = Hash::make($data['password']);
        }

        $user->update($updateData);

        return redirect()->route('admin.users.pengurus')->with('success', 'Data pengurus berhasil diperbarui!');
    }

    public function destroyPengurus($id)
    {
        $user = User::findOrFail($id);
        $user->delete();
        return redirect()->route('admin.users.pengurus')->with('success', 'Akun pengurus berhasil dihapus.');
    }

    // ========== Manajemen Customer / Pembeli (tb_customer) ==========
    public function indexCustomer()
    {
        $customers = Customer::withCount('penjualans')->orderBy('nama_customer')->paginate(20);
        return view('admin.users.customer', compact('customers'));
    }

    public function destroyCustomer($id)
    {
        $customer = Customer::findOrFail($id);
        $customer->delete();
        return redirect()->route('admin.users.customer')->with('success', 'Akun customer berhasil dihapus.');
    }
}
