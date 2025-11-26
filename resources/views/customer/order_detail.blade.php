@extends('layouts.app')

@section('content')
<div class="container py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2><i class="bi bi-file-text-fill me-2"></i> Detail Pesanan #{{ $order->id }}</h2>
        <a href="{{ URL::previous() }}" class="btn btn-secondary"><i class="bi bi-arrow-left"></i> Kembali</a>
    </div>

    <div class="row">
        <div class="col-md-7">
            {{-- Bagian Detail Item --}}
            <div class="card shadow-sm mb-4">
                <div class="card-header bg-light">
                    <h5>Ringkasan Item</h5>
                </div>
                <ul class="list-group list-group-flush">
                    @foreach ($order->items as $item)
                    <li class="list-group-item d-flex justify-content-between">
                        <div>
                            <strong>{{ $item->quantity }}x {{ $item->product->name ?? 'Produk Dihapus' }}</strong>
                            @if ($item->price != $item->product->price)
                                <span class="badge bg-warning ms-2">Diskon</span>
                            @endif
                        </div>
                        <span>Rp {{ number_format($item->price * $item->quantity) }}</span>
                    </li>
                    @endforeach
                </ul>
                <div class="card-footer d-flex justify-content-between fw-bold fs-5">
                    <span>Total Tagihan:</span>
                    <span class="text-danger">Rp {{ number_format($order->total_amount) }}</span>
                </div>
            </div>
            
            {{-- Detail Pengiriman/Meja --}}
            <div class="card shadow-sm">
                <div class="card-header bg-light">
                    <h5>Informasi Tambahan</h5>
                </div>
                <div class="card-body">
                    <p>Tipe Order: <strong>{{ strtoupper($order->order_type) }}</strong></p>
                    @if($order->order_type == 'dine_in')
                        <p>Meja: <strong>{{ $order->table->table_number ?? 'N/A' }}</strong></p>
                    @elseif($order->delivery_address)
                        <p>Alamat Pengiriman: <strong>{{ $order->delivery_address }}</strong></p>
                    @endif
                    <p>Dipesan oleh: <strong>{{ $order->user->name ?? 'Dine-In Customer' }}</strong></p>
                </div>
            </div>
        </div>

        <div class="col-md-5">
            {{-- Bagian Status dan Pembayaran --}}
            <div class="card shadow-lg bg-light">
                <div class="card-body text-center">
                    <h4>Status Transaksi</h4>
                    <h1 class="my-3">
                        @if($order->payment_status == 'paid' || $order->payment_status == 'settlement')
                            <span class="badge bg-success"><i class="bi bi-check-circle"></i> LUNAS</span>
                        @elseif($order->payment_status == 'pending')
                            <span class="badge bg-warning text-dark"><i class="bi bi-clock"></i> PENDING</span>
                        @else
                            <span class="badge bg-danger"><i class="bi bi-x-octagon"></i> GAGAL</span>
                        @endif
                    </h1>

                    @if($order->payment_status == 'pending' && $order->midtrans_snap_token)
                        <p class="text-muted">Pembayaran belum selesai. Klik tombol di bawah untuk melanjutkan pembayaran.</p>
                        
                        <script type="text/javascript" 
                            @if(env('MIDTRANS_IS_PRODUCTION'))
                                src="https://app.midtrans.com/snap/snap.js" 
                            @else
                                src="https://app.sandbox.midtrans.com/snap/snap.js" 
                            @endif
                            data-client-key="{{ env('MIDTRANS_CLIENT_KEY') }}">
                        </script>

                        <button id="pay-button" class="btn btn-primary btn-lg mt-3">
                            <i class="bi bi-wallet2"></i> Bayar Sekarang (Rp {{ number_format($order->total_amount) }})
                        </button>
                        <form action="{{ route('customer.order.cancel', $order->id) }}" method="POST" class="d-inline">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger btn-lg mt-3" 
                            onclick="return confirm('Apakah Anda yakin ingin membatalkan pesanan ini? Stok akan dikembalikan.');">
                        <i class="bi bi-x-circle"></i> Batal Pesanan
                    </button>
                </form>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Skrip Pembayaran Midtrans (Hanya jika status pending) --}}
@if($order->payment_status == 'pending' && $order->midtrans_snap_token)
<script>
    const snapToken = '{{ $order->midtrans_snap_token }}';
    document.getElementById('pay-button').onclick = function(){
        snap.pay(snapToken, {
            onSuccess: function(result){
                alert("Pembayaran Berhasil! Halaman akan dimuat ulang.");
                window.location.reload(); 
            },
            onError: function(result){
                alert("Pembayaran Gagal.");
            },
            onClose: function(){
                // User menutup pop-up, status order tetap pending
            }
        });
    };
</script>
@endif
@endsection