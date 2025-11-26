@extends('layouts.app')

@section('content')
<div class="container py-4">
    <h2><i class="bi bi-cart-fill"></i> Keranjang Pesanan</h2>
    <p>Review pesanan Anda sebelum melanjutkan ke pembayaran.</p>

    @if(empty($cart))
        <div class="alert alert-warning text-center">
            Keranjang Anda kosong.
            <a href="{{ route('customer.menu', session('table_id') ?? 1) }}" class="alert-link">Kembali ke Menu</a>
        </div>
    @else
        <form action="{{ route('customer.cart.update') }}" method="POST">
            @csrf
            @method('PUT')
            
            <ul class="list-group mb-4">
                @foreach($cart as $id => $item)
                <li class="list-group-item d-flex justify-content-between align-items-center">
                    <div>
                        <h5 class="mb-1">{{ $item['name'] }}</h5>
                        <small class="text-muted">Rp {{ number_format($item['price']) }} / item</small>
                    </div>
                    
                    <div class="d-flex align-items-center">
                        <input type="number" name="items[{{ $id }}][quantity]" 
                               value="{{ $item['quantity'] }}" min="1" class="form-control form-control-sm me-2" style="width: 70px;">
                        
                        <div class="text-end me-3">
                            <small class="d-block">Subtotal</small>
                            <strong>Rp {{ number_format($item['price'] * $item['quantity']) }}</strong>
                        </div>
                        
                        <a href="{{ route('customer.cart.remove', $id) }}" class="btn btn-outline-danger btn-sm" title="Hapus">
                            <i class="bi bi-trash"></i>
                        </a>
                    </div>
                </li>
                @endforeach
            </ul>

            <div class="d-flex justify-content-between mb-4 p-3 bg-light rounded shadow-sm">
                <h4>Total Tagihan:</h4>
                <h4 class="text-primary">Rp {{ number_format($total) }}</h4>
            </div>

            <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                <button type="submit" class="btn btn-warning"><i class="bi bi-arrow-repeat"></i> Perbarui Jumlah</button>
                
                <a href="#checkoutModal" class="btn btn-success" data-bs-toggle="modal"><i class="bi bi-credit-card"></i> Lanjutkan Pembayaran</a>
            </div>
        </form>

        @include('partials.checkout_modal')

    @endif
</div>
@endsection