<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\ProdukController;
use App\Http\Controllers\PemasokController;
use App\Http\Controllers\PasokanController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\PenjualanController;
use App\Http\Controllers\LaporanController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\MidtransWebhookController;

// Webhook Notifikasi HTTP Midtrans (Bypass CSRF)
Route::post('/midtrans/notification', [MidtransWebhookController::class, 'handle'])->name('midtrans.notification');

// Halaman Publik (Toko Beras Organik)
Route::get('/', [HomeController::class, 'index'])->name('home');
Route::get('/produk/{id}', [HomeController::class, 'detail'])->name('produk.detail');

// Keranjang Belanja
Route::get('/keranjang', [CartController::class, 'index'])->name('cart.index');
Route::post('/keranjang/tambah/{id}', [CartController::class, 'add'])->name('cart.add');
Route::get('/keranjang/tambah-qty/{id}', [CartController::class, 'increase'])->name('cart.increase');
Route::get('/keranjang/kurang-qty/{id}', [CartController::class, 'decrease'])->name('cart.decrease');
Route::post('/keranjang/update/{id}', [CartController::class, 'update'])->name('cart.update');
Route::get('/keranjang/hapus/{id}', [CartController::class, 'remove'])->name('cart.remove');

// Checkout & Pembayaran
Route::post('/checkout', [CartController::class, 'checkout'])->name('checkout.process');
Route::get('/pembayaran/{id}', [CartController::class, 'paymentShow'])->name('payment.show');
Route::post('/pembayaran/{id}/konfirmasi', [CartController::class, 'confirmPayment'])->name('payment.confirm');
Route::post('/pembayaran/upload-qris', [CartController::class, 'uploadCustomQris'])->name('payment.upload-qris');

// Autentikasi Pembeli (Customer)
Route::get('/login', [AuthController::class, 'showCustomerLogin'])->name('login');
Route::post('/login', [AuthController::class, 'customerLogin'])->name('login.post');
Route::get('/register', [AuthController::class, 'showCustomerRegister'])->name('register');
Route::post('/register', [AuthController::class, 'customerRegister'])->name('register.post');

// Autentikasi Pengurus (Ketua & Bendahara)
Route::get('/admin/login', [AuthController::class, 'showAdminLogin'])->name('admin.login');
Route::post('/admin/login', [AuthController::class, 'adminLogin'])->name('admin.login.post');

// Quick Demo 1-Click Login
Route::get('/demo-login/{role}', [AuthController::class, 'demoLogin'])->name('demo.login');

// Logout Global
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// Area Pembeli / Customer Terautentikasi (Riwayat Pesanan & Profil)
Route::middleware(['auth:customer'])->group(function () {
    Route::get('/profil', [\App\Http\Controllers\CustomerProfileController::class, 'show'])->name('customer.profile');
    Route::put('/profil', [\App\Http\Controllers\CustomerProfileController::class, 'update'])->name('customer.profile.update');
    Route::get('/pesanan', [OrderController::class, 'riwayat'])->name('orders.riwayat');
    Route::get('/pesanan/{id}/struk', [OrderController::class, 'struk'])->name('orders.struk');
    Route::post('/pesanan/{id}/simulasi', [OrderController::class, 'simulasiPembayaran'])->name('orders.simulasi');
});

// Area Pengurus (Ketua & Bendahara)
Route::prefix('admin')->middleware(['auth:web'])->group(function () {
    // Dashboard & Tindakan Cepat
    Route::get('/dashboard', [AdminController::class, 'dashboard'])->name('admin.dashboard');
    Route::post('/penjualan/konfirmasi-cash/{id}', [AdminController::class, 'konfirmasiCash'])->name('admin.konfirmasi-cash');

    // CRUD Produk Beras
    Route::get('/produk', [ProdukController::class, 'index'])->name('admin.produk.index');
    Route::get('/produk/create', [ProdukController::class, 'create'])->name('admin.produk.create');
    Route::post('/produk', [ProdukController::class, 'store'])->name('admin.produk.store');
    Route::get('/produk/{id}/edit', [ProdukController::class, 'edit'])->name('admin.produk.edit');
    Route::put('/produk/{id}', [ProdukController::class, 'update'])->name('admin.produk.update');
    Route::delete('/produk/{id}', [ProdukController::class, 'destroy'])->name('admin.produk.destroy');

    // CRUD Pemasok
    Route::get('/pemasok', [PemasokController::class, 'index'])->name('admin.pemasok.index');
    Route::get('/pemasok/create', [PemasokController::class, 'create'])->name('admin.pemasok.create');
    Route::post('/pemasok', [PemasokController::class, 'store'])->name('admin.pemasok.store');
    Route::get('/pemasok/{id}/edit', [PemasokController::class, 'edit'])->name('admin.pemasok.edit');
    Route::put('/pemasok/{id}', [PemasokController::class, 'update'])->name('admin.pemasok.update');
    Route::delete('/pemasok/{id}', [PemasokController::class, 'destroy'])->name('admin.pemasok.destroy');

    // Kelola Pembelian / Pasokan (FIFO Batch)
    Route::get('/pasokan', [PasokanController::class, 'index'])->name('admin.pasokan.index');
    Route::get('/pasokan/create', [PasokanController::class, 'create'])->name('admin.pasokan.create');
    Route::post('/pasokan', [PasokanController::class, 'store'])->name('admin.pasokan.store');
    Route::delete('/pasokan/{id}', [PasokanController::class, 'destroy'])->name('admin.pasokan.destroy');

    // Kelola Penjualan & Konfirmasi Pembayaran
    Route::get('/penjualan', [PenjualanController::class, 'index'])->name('admin.penjualan.index');
    Route::get('/penjualan/{id}', [PenjualanController::class, 'show'])->name('admin.penjualan.show');
    Route::post('/penjualan/{id}/status', [PenjualanController::class, 'updateStatus'])->name('admin.penjualan.status');
    Route::post('/penjualan/{id}/konfirmasi', [PenjualanController::class, 'konfirmasiPembayaran'])->name('admin.penjualan.konfirmasi');
    Route::post('/penjualan/{id}/tolak', [PenjualanController::class, 'tolakPembayaran'])->name('admin.penjualan.tolak');

    // Pengguna (Ketua: Pengurus & Customer)
    Route::get('/users/pengurus', [UserController::class, 'indexPengurus'])->name('admin.users.pengurus');
    Route::get('/users/pengurus/create', [UserController::class, 'createPengurus'])->name('admin.users.pengurus.create');
    Route::post('/users/pengurus', [UserController::class, 'storePengurus'])->name('admin.users.pengurus.store');
    Route::get('/users/pengurus/{id}/edit', [UserController::class, 'editPengurus'])->name('admin.users.pengurus.edit');
    Route::put('/users/pengurus/{id}', [UserController::class, 'updatePengurus'])->name('admin.users.pengurus.update');
    Route::delete('/users/pengurus/{id}', [UserController::class, 'destroyPengurus'])->name('admin.users.pengurus.destroy');
    Route::get('/users/customer', [UserController::class, 'indexCustomer'])->name('admin.users.customer');
    Route::delete('/users/customer/{id}', [UserController::class, 'destroyCustomer'])->name('admin.users.customer.destroy');

    // Laporan Lengkap
    Route::get('/laporan/penjualan', [LaporanController::class, 'penjualan'])->name('admin.laporan.penjualan');
    Route::get('/laporan/stok', [LaporanController::class, 'stok'])->name('admin.laporan.stok');
    Route::get('/laporan/pembelian', [LaporanController::class, 'pembelian'])->name('admin.laporan.pembelian');
    Route::get('/laporan/laba-rugi', [LaporanController::class, 'labaRugi'])->name('admin.laporan.laba-rugi');
});
