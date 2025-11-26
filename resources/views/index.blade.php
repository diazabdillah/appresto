@extends('layouts.app')

@section('content')
<div class="container py-5 text-center">
    <div class="p-5 mb-4 bg-light rounded-3 shadow-lg">
        <div class="container-fluid py-5">
            <h1 class="display-5 fw-bold text-primary"><i class="bi bi-cup-hot-fill me-3"></i> Selamat Datang di Restoran Kami!</h1>
            <p class="fs-4 mt-4 mb-5">
                Pesan menu favorit Anda dengan cepat menggunakan sistem digital kami.
            </p>
            
            <div class="row justify-content-center">
                
                {{-- DINE-IN (QR SCAN) CARD --}}
                <div class="col-lg-6">
                    <div class="alert alert-success p-4 border border-3 border-success h-100 d-flex flex-column justify-content-between">
                        <div>
                            <i class="bi bi-qr-code-scan display-4 mb-3 d-block"></i>
                            <h4 class="fw-bold">Pemesanan DINE-IN (Paling Cepat):</h4>
                            <p class="lead">
                                Cukup **SCAN QR CODE** yang ada di meja Anda. Anda akan langsung diarahkan ke menu tanpa perlu login.
                            </p>
                        </div>
                        
                        <div class="mt-4">
                            <a href="{{ route('customer.scan_qr') }}" class="btn btn-success btn-lg shadow-sm">
                                <i class="bi bi-camera-fill me-2"></i> **SCAN QR CODE SEKARANG**
                            </a>
                        </div>
                    </div>
                </div>
                
                {{-- DELIVERY / TAKEAWAY CARD (Perubahan Utama di Sini) --}}
                <div class="col-lg-6">
                    <div class="alert alert-warning p-4 border border-3 border-warning h-100 d-flex flex-column justify-content-between">
                        <div>
                            <i class="bi bi-truck display-4 mb-3 d-block"></i>
                            <h4 class="fw-bold">Pemesanan DELIVERY / TAKEAWAY:</h4>
                            
                            {{-- LOGIKA DISPLAY BERDASARKAN STATUS LOGIN --}}
                            @auth
                                {{-- Jika User SUDAH LOGIN --}}
                                <p class="lead text-dark">
                                    Selamat datang kembali, {{ Auth::user()->name }}. Silakan mulai order Anda.
                                </p>
                            @else
                                {{-- Jika User BELUM LOGIN --}}
                                <p class="lead">
                                    Untuk memesan dari luar, silakan <a href="{{ route('login') }}" class="alert-link">LOGIN</a> atau <a href="{{ route('register') }}" class="alert-link">DAFTAR</a> untuk mengelola alamat dan riwayat pesanan Anda.
                                </p>
                            @endauth
                        </div>
                        
                        <div class="mt-4">
                            @auth
                                {{-- Jika LOGIN: Tombol Arahkan ke Menu Utama (atau keranjang jika sudah ada item) --}}
                                <a href="{{ route('customer.menu.delivery') }}" class="btn btn-primary btn-lg shadow-sm">
                                    <i class="bi bi-arrow-right-circle me-2"></i> MULAI ORDER DELIVERY
                                </a>
                            @else
                                {{-- Jika BELUM LOGIN: Tombol Arahkan ke Login --}}
                                <a href="{{ route('login') }}" class="btn btn-warning btn-lg shadow-sm">
                                    Login untuk Delivery
                                </a>
                            @endauth
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>
</div>
@endsection