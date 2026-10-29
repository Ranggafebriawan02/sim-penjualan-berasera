<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class CustomerProfileController extends Controller
{
    public function show()
    {
        $customer = Auth::guard('customer')->user();
        return view('customer.profile', compact('customer'));
    }

    public function update(Request $request)
    {
        $customer = Auth::guard('customer')->user();

        $data = $request->validate([
            'nama_customer' => 'required|string|max:100',
            'telepon'       => 'required|string|max:25',
            'email'         => ['required', 'email', 'max:100', Rule::unique('tb_customer', 'email')->ignore($customer->id_customer, 'id_customer')],
            'username'      => ['required', 'string', 'max:50', Rule::unique('tb_customer', 'username')->ignore($customer->id_customer, 'id_customer')],
            'alamat'        => 'required|string|min:5',
            'password'      => 'nullable|string|min:6|confirmed',
        ]);

        $updatePayload = [
            'nama_customer' => $data['nama_customer'],
            'telepon'       => $data['telepon'],
            'email'         => $data['email'],
            'username'      => $data['username'],
            'alamat'        => $data['alamat'],
        ];

        if (!empty($data['password'])) {
            $updatePayload['password'] = Hash::make($data['password']);
        }

        Customer::where('id_customer', $customer->id_customer)->update($updatePayload);

        return back()->with('success', 'Profil dan alamat pengiriman berhasil diperbarui!');
    }
}

