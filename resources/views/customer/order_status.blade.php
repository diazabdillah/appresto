@extends('layouts.app')

@section('content')
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card shadow-lg">
                <div class="card-header bg-dark text-white text-center">
                    <h4>Status Pesanan Anda #{{ $order->id }}</h4>
                </div>
                <div class="card-body text-center">
                    
                    @if($order->payment_status === 'paid' || $order->payment_status === 'settlement')
                        <i class="bi bi-check-circle-fill text-success display-1 mb-3"></i>
                        <h3 class="text-success">Pembayaran Berhasil!</h3>
                        <p class="lead">Pesanan Anda kini sedang diproses oleh dapur kami. Mohon tunggu.</p>
                        
                    @elseif($order->payment_status === 'pending')
                        <i class="bi bi-clock-fill text-warning display-1 mb-3"></i>
                        <h3 class="text-warning">Menunggu Pembayaran</h3>
                        <p class="lead">Kami menunggu konfirmasi pembayaran Midtrans Anda. Silakan selesaikan pembayaran sesuai instruksi yang muncul sebelumnya.</p>
                        <p class="small text-muted">Total Tagihan: Rp {{ number_format($order->total_amount) }}</p>
                        
                    @else
                        <i class="bi bi-x-circle-fill text-danger display-1 mb-3"></i>
                        <h3 class="text-danger">Transaksi Gagal/Kedaluwarsa</h3>
                        <p class="lead">Silakan buat pesanan baru atau hubungi staf kami untuk bantuan.</p>
                    @endif
                    
                    <hr>
                    <a href="{{ route('customer.menu', $order->table_id ?? 1) }}" class="btn btn-primary mt-3">Pesan Lagi</a>
                    @auth
                        <a href="{{ route('app.orders.history') }}" class="btn btn-secondary mt-3">Lihat Riwayat Saya</a>
                    @endauth
                </div>
            </div>
        </div>
    </div>
</div>
@endsection