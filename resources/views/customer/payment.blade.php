@extends('layouts.app')

@section('content')
<div class="container py-5">
    <div class="card shadow-lg text-center">
        <!-- ... (Bagian header dan body lainnya) ... -->
        
        <div class="card-body">
            
            <!-- ... (Detail order) ... -->
            
            <h1 class="my-4 text-danger">Total: Rp {{ number_format($order->total_amount) }}</h1>
            
            <p class="lead">Klik tombol di bawah untuk menyelesaikan pembayaran via Midtrans.</p>
            
            <!-- Midtrans Snap Script (Menggunakan Conditional URL) -->
            <script type="text/javascript" 
                @if(env('MIDTRANS_IS_PRODUCTION'))
                    src="https://app.midtrans.com/snap/snap.js" 
                @else
                    src="https://app.sandbox.midtrans.com/snap/snap.js" 
                @endif
                data-client-key="{{ env('MIDTRANS_CLIENT_KEY') }}">
            </script>

            <button id="pay-button" class="btn btn-success btn-lg mt-3">
                <i class="bi bi-shield-lock"></i> Bayar Sekarang
            </button>

        </div>
        <!-- ... (Bagian footer) ... -->
    </div>
</div>

<script type="text/javascript">
    const snapToken = '{{ $order->midtrans_snap_token }}';
    
    document.getElementById('pay-button').onclick = function(){
        snap.pay(snapToken, {
            onSuccess: function(result){
                alert("Pembayaran Berhasil! Pesanan Anda segera diproses.");
                // Menggunakan route untuk histori (sesuai skenario dine-in atau user login)
                @if(Auth::check())
                    window.location.href = '{{ route("app.orders.history") }}';
                @else
                    window.location.href = '{{ route("customer.table.history") }}';
                @endif
            },
            onError: function(result){
                alert("Pembayaran Gagal. Silakan coba lagi atau hubungi staf.");
            },
            onClose: function(){
                alert('Anda menutup pop-up Midtrans. Pesanan Anda tetap berstatus pending.');
            }
        });
    };
</script>
@endsection