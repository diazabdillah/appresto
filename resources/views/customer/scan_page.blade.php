@extends('layouts.app')

@section('content')
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-8 text-center">
            <h2 class="mb-4">Arahkan Kamera ke QR Code Meja</h2>
            
            {{-- Elemen Video tempat kamera akan ditampilkan --}}
            <video id="preview" style="width: 100%; max-width: 600px; border: 3px solid #007bff; border-radius: 8px;"></video>
            
            <p class="mt-3 text-muted">Scanning...</p>
            <div id="status-message" class="alert alert-info" style="display: none;"></div>
            
            <a href="{{ url('/') }}" class="btn btn-secondary mt-3">Batalkan Scan</a>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    let scanner = new Instascan.Scanner({ video: document.getElementById('preview') });
    let statusMessage = document.getElementById('status-message');

    // 1. Dapatkan izin kamera
    Instascan.Camera.getCameras().then(function (cameras) {
        if (cameras.length > 0) {
            scanner.start(cameras[0]); // Mulai scan dengan kamera pertama
        } else {
            statusMessage.style.display = 'block';
            statusMessage.className = 'alert alert-danger';
            statusMessage.innerHTML = 'Kamera tidak ditemukan atau akses ditolak.';
            console.error('Kamera tidak ditemukan.');
        }
    }).catch(function (e) {
        statusMessage.style.display = 'block';
        statusMessage.className = 'alert alert-danger';
        statusMessage.innerHTML = 'Akses kamera ditolak. Mohon izinkan akses kamera pada browser Anda.';
        console.error(e);
    });

    // 2. Logika setelah QR Code berhasil dibaca
    scanner.addListener('scan', function (content) {
        scanner.stop(); // Hentikan scanner setelah berhasil

        statusMessage.style.display = 'block';
        statusMessage.className = 'alert alert-success';
        statusMessage.innerHTML = 'QR Code terdeteksi! Mengalihkan...';
        
        // Asumsi QR Code berisi URL lengkap ke route menu: 
        // Contoh: http://app.test/menu/5
        
        window.location.href = content; 
    });
});
</script>
@endsection