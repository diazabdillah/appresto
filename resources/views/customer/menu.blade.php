@extends('layouts.app') 

@section('content')
<div class="container py-4">
    <div class="alert alert-info text-center sticky-top mb-4">
        Anda berada di Meja No. {{ $table->table_number }}. 
        <a href="{{ route('customer.cart.show') }}" class="btn btn-sm btn-warning float-end">
            <i class="bi bi-cart"></i> Keranjang (<span id="cart-count">{{ count(session('cart', [])) }}</span>)
        </a>
    </div>

    {{-- ⚡ FLASH SALE SECTION --}}
    @if($flashSaleProducts->count())
    <div class="p-3 mb-4 bg-danger text-white rounded shadow-sm">
        <h3 class="mb-3"><i class="bi bi-lightning-fill"></i> FLASH SALE!</h3>
        <p>Berakhir dalam: <strong id="countdown">00j 00m 00d</strong></p>
        <div class="row row-cols-2 row-cols-md-4 g-3">
            @foreach($flashSaleProducts as $product)
                @include('partials.product_card', ['product' => $product]) 
            @endforeach
        </div>
    </div>
    @endif
    
    <hr class="my-5">

    {{-- ⭐ PRODUK BEST SELLER --}}
    @if($bestSellerProducts->count())
    <div class="product-section mb-5">
        <h3 class="mb-3"><i class="bi bi-star-fill text-warning me-2"></i> Produk Best Seller</h3>
        <div class="row row-cols-2 row-cols-md-4 g-3">
            @foreach($bestSellerProducts as $product)
                @include('partials.product_card', ['product' => $product]) 
            @endforeach
        </div>
    </div>
    @endif

    {{-- ✨ PRODUK TERBARU --}}
    @if($newProducts->count())
    <div class="product-section mb-5">
        <h3 class="mb-3"><i class="bi bi-gem text-info me-2"></i> Produk Terbaru</h3>
        <div class="row row-cols-2 row-cols-md-4 g-3">
            @foreach($newProducts as $product)
                @include('partials.product_card', ['product' => $product]) 
            @endforeach
        </div>
    </div>
    @endif

    <hr class="my-5">

    {{-- 🍔 MENU BERDASARKAN KATEGORI --}}
    @foreach($categories as $category)
        @if($category->products->count())
        <h3 class="mt-5 mb-3 border-bottom pb-2">{{ $category->name }}</h3>
        <div class="row row-cols-2 row-cols-md-4 g-3">
            @foreach($category->products as $product)
                @include('partials.product_card', ['product' => $product]) 
            @endforeach
        </div>
        @endif
    @endforeach

</div>

{{-- Tambahkan JavaScript untuk Countdown Flash Sale di sini (jika menggunakan js) --}}
@endsection