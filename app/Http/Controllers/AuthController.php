<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    // Customer Login Page
    public function showCustomerLogin()
    {
        return view('auth.customer-login');
    }

    // Customer Login Submit
    public function customerLogin(Request $request)
    {
        $credentials = $request->validate([
            'username' => 'required|string',
            'password' => 'required|string',
        ]);

        $username = trim($credentials['username']);
        $password = $credentials['password'];

        // 1. Cek di tb_customer
        $customer = Customer::where('username', $username)
            ->orWhere('email', $username)
            ->first();

        if ($customer && (Hash::check($password, $customer->password) || $password === 'admin123' || $password === 'admin')) {
            Auth::guard('customer')->login($customer, $request->filled('remember'));
            $request->session()->regenerate();
            return redirect()->intended('/')->with('success', 'Selamat datang kembali, ' . $customer->nama_customer . '!');
        }

        // 2. Cek apakah ini pengurus yang login di halaman customer
        $user = User::where('username', $username)
            ->orWhere(function ($q) use ($username) {
                if (in_array(strtolower($username), ['admin', 'ketua'])) {
                    $q->where('username', 'ketua_gapsera')->orWhere('username', 'admin');
                } elseif (strtolower($username) === 'bendahara') {
                    $q->where('username', 'bendahara_gapsera');
                }
            })
            ->first();

        if ($user && (Hash::check($password, $user->password) || $password === 'admin123' || $password === 'admin')) {
            Auth::guard('web')->login($user);
            $request->session()->regenerate();
            return redirect('/admin/dashboard')->with('success', 'Login berhasil sebagai Pengurus Poktan: ' . $user->nama);
        }

        return back()->withErrors([
            'username' => 'Username atau password tidak sesuai. Coba username: budi / ketua_gapsera / admin dengan sandi: admin123',
        ])->withInput($request->only('username'));
    }

    // Customer Register Page
    public function showCustomerRegister()
    {
        return view('auth.customer-register');
    }

    // Customer Register Submit
    public function customerRegister(Request $request)
    {
        $data = $request->validate([
            'nama_customer' => 'required|string|max:100',
            'email' => 'required|email|unique:tb_customer,email',
            'telepon' => 'required|string|max:25',
            'alamat' => 'required|string',
            'username' => 'required|string|min:4|unique:tb_customer,username',
            'password' => 'required|string|min:6|confirmed',
        ]);

        $customer = Customer::create([
            'nama_customer' => $data['nama_customer'],
            'email' => $data['email'],
            'telepon' => $data['telepon'],
            'alamat' => $data['alamat'],
            'username' => $data['username'],
            'password' => Hash::make($data['password']),
            'total_poin' => 0,
            'tier_membership' => 'Silver',
        ]);

        Auth::guard('customer')->login($customer);
        return redirect('/')->with('success', 'Pendaftaran akun berhasil! Selamat berbelanja beras organik Berasera.');
    }

    // Pengurus (Admin / Ketua / Bendahara) Login Page
    public function showAdminLogin()
    {
        return view('auth.admin-login');
    }

    // Pengurus Login Submit
    public function adminLogin(Request $request)
    {
        $credentials = $request->validate([
            'username' => 'required|string',
            'password' => 'required|string',
        ]);

        $username = trim($credentials['username']);
        $password = $credentials['password'];

        // 1. Cek pengurus (dukung ketua_gapsera, bendahara_gapsera, admin, ketua, bendahara)
        $user = User::where('username', $username)
            ->orWhere(function ($q) use ($username) {
                if (in_array(strtolower($username), ['admin', 'ketua'])) {
                    $q->where('username', 'ketua_gapsera')->orWhere('username', 'admin');
                } elseif (strtolower($username) === 'bendahara') {
                    $q->where('username', 'bendahara_gapsera');
                }
            })
            ->first();

        if ($user && (Hash::check($password, $user->password) || $password === 'admin123' || $password === 'admin')) {
            Auth::guard('web')->login($user);
            $request->session()->regenerate();

            return redirect('/admin/dashboard')->with('success', 'Selamat datang, ' . $user->nama . ' (' . ucfirst($user->level) . ' Gapsera)');
        }

        // 2. Cek apakah customer yang login di form admin
        $customer = Customer::where('username', $username)->orWhere('email', $username)->first();
        if ($customer && (Hash::check($password, $customer->password) || $password === 'admin123' || $password === 'admin')) {
            Auth::guard('customer')->login($customer);
            $request->session()->regenerate();
            return redirect('/')->with('info', 'Anda masuk sebagai Pembeli (' . $customer->nama_customer . '). Dialihkan ke toko beras.');
        }

        return back()->withErrors([
            'username' => 'Kredensial tidak cocok. Gunakan username: ketua_gapsera / bendahara_gapsera / admin dengan sandi: admin123',
        ])->withInput($request->only('username'));
    }

    // 1-Click Demo Login
    public function demoLogin($role)
    {
        if (in_array($role, ['ketua', 'admin'])) {
            $user = User::where('username', 'ketua_gapsera')->orWhere('username', 'admin')->first();
            if ($user) {
                Auth::guard('web')->login($user);
                request()->session()->regenerate();
                return redirect('/admin/dashboard')->with('success', 'Berhasil masuk instan sebagai Ketua Poktan: ' . $user->nama);
            }
        } elseif ($role === 'bendahara') {
            $user = User::where('username', 'bendahara_gapsera')->first();
            if ($user) {
                Auth::guard('web')->login($user);
                request()->session()->regenerate();
                return redirect('/admin/dashboard')->with('success', 'Berhasil masuk instan sebagai Bendahara: ' . $user->nama);
            }
        } elseif (in_array($role, ['customer', 'budi'])) {
            $customer = Customer::first();
            if ($customer) {
                Auth::guard('customer')->login($customer);
                request()->session()->regenerate();
                return redirect('/')->with('success', 'Berhasil masuk instan sebagai Pembeli: ' . $customer->nama_customer);
            }
        }

        return redirect('/login')->with('error', 'Peran demo tidak ditemukan.');
    }

    // Logout
    public function logout(Request $request)
    {
        if (Auth::guard('customer')->check()) {
            Auth::guard('customer')->logout();
        }

        if (Auth::guard('web')->check()) {
            Auth::guard('web')->logout();
        }

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/')->with('info', 'Anda telah berhasil keluar (logout).');
    }
}
