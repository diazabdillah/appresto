@extends('layouts.app') 

@section('content')
<div class="container py-4">

    {{-- 🌟 BANNER SLIDER JUMBO (Bootstrap Carousel) 🌟 --}}
    <div id="heroCarousel" class="carousel slide mb-5 shadow-lg rounded overflow-hidden" data-bs-ride="carousel">
        <div class="carousel-indicators">
            <button type="button" data-bs-target="#heroCarousel" data-bs-slide-to="0" class="active" aria-current="true" aria-label="Slide 1"></button>
            <button type="button" data-bs-target="#heroCarousel" data-bs-slide-to="1" aria-label="Slide 2"></button>
            <button type="button" data-bs-target="#heroCarousel" data-bs-slide-to="2" aria-label="Slide 3"></button>
        </div>
        
        <div class="carousel-inner">
            <div class="carousel-item active">
                <img src="{{ asset('images/banner1.jpg') }}" class="d-block w-100" alt="Banner Promo 1" style="height: 300px; object-fit: cover;">
                <div class="carousel-caption d-none d-md-block bg-dark bg-opacity-50 p-2 rounded">
                    <h5>Promo Spesial Hari Ini!</h5>
                    <p>Diskon besar untuk semua Makanan Utama.</p>
                </div>
            </div>
            <div class="carousel-item">
                <img src="{{ asset('images/banner2.jpg') }}" class="d-block w-100" alt="Banner Promo 2" style="height: 300px; object-fit: cover;">
                <div class="carousel-caption d-none d-md-block bg-dark bg-opacity-50 p-2 rounded">
                    <h5>Menu Baru!</h5>
                    <p>Coba menu terbaru kami, dijamin ketagihan!</p>
                </div>
            </div>
            <div class="carousel-item">
                <img src="{{ asset('images/banner3.jpg') }}" class="d-block w-100" alt="Banner Promo 3" style="height: 300px; object-fit: cover;">
                <div class="carousel-caption d-none d-md-block bg-dark bg-opacity-50 p-2 rounded">
                    <h5>Dapatkan Cashback!</h5>
                    <p>Bayar pakai metode X, dapatkan cashback 10%.</p>
                </div>
            </div>
        </div>

        <button class="carousel-control-prev" type="button" data-bs-target="#heroCarousel" data-bs-slide="prev">
            <span class="carousel-control-prev-icon" aria-hidden="true"></span>
            <span class="visually-hidden">Previous</span>
        </button>
        <button class="carousel-control-next" type="button" data-bs-target="#heroCarousel" data-bs-slide="next">
            <span class="carousel-control-next-icon" aria-hidden="true"></span>
            <span class="visually-hidden">Next</span>
        </button>
    </div>
    
    {{-- --- SLIDER END --- --}}

    {{-- 🏷️ MENU BERDASARKAN KATEGORI (Tampilan Bulat) --}}
@if($categories->count())
<div class="category-section mb-5 shadow-sm p-3 rounded bg-white">
    <h3 class="mb-3 text-center"><i class="bi bi-tags-fill me-2"></i> Jelajahi Kategori</h3>
    <div class="d-flex flex-row flex-nowrap overflow-auto p-2" style="-webkit-overflow-scrolling: touch;">
        @foreach($categories as $category)
            @php
                // Mapping kata kunci ke Bootstrap Icons (case-insensitive)
                $iconMap = [
                    'makanan' => 'cup-hot-fill',    // Diubah ke ikon makanan/minuman panas
                    'panas'   => 'cup-hot-fill',    // Tambahkan alias 'panas'

                    'minuman' => 'cup-straw', 
                    'kopi' => 'cup-hot',
                    'teh' => 'cup-hot',
                    
                    'jus' => 'droplet-half',
                    'sayur' => 'apple',
                    'buah' => 'tropical-fish',
                    'seafood' => 'tropical-fish',
                    'ikan' => 'tropical-fish',
                    'ayam' => 'bucket-fill',
                    'daging' => 'egg-fried', 
                    'nasi' => 'bag-fill',
                    'mie' => 'egg-fried',
                    'pedas' => 'fire',
                    'promo' => 'lightning-fill',
                    'flash sale' => 'lightning-fill',
                    'best seller' => 'star-fill',
                    'snack' => 'bagel-fill',
                    'dessert' => 'cake-fill',
                    
                    'default' => 'grid-fill' 
                ];
                
                $categoryName = strtolower($category->name);
                $iconMatch = $iconMap['default']; 
                
                // Cari kata kunci yang cocok di dalam nama kategori
                foreach ($iconMap as $keyword => $icon) {
                    if (str_contains($categoryName, $keyword)) {
                        $iconMatch = $icon;
                        break; 
                    }
                }

                $iconClass = 'bi-' . $iconMatch;
            @endphp
            
            <div class="text-center mx-2 flex-shrink-0" style="width: 100px;">
                <a href="{{ route('customer.category.show', $category) }}" class="text-decoration-none text-dark d-block">
                    <div class="rounded-circle d-flex align-items-center justify-content-center mx-auto mb-1" 
                         style="width: 70px; height: 70px; background-color: #f8f9fa; border: 2px solid #ced4da;">
                        
                        <i class="bi {{ $iconClass }} text-secondary" style="font-size: 2rem;"></i>
                    </div>
                    <small class="fw-bold">{{ $category->name }}</small>
                </a>
            </div>
        @endforeach
    </div>
</div>
@endif
    
    {{-- ⚡ FLASH SALE SECTION --}}
    @if($flashSaleProducts->count())
    <hr class="my-5">
    <div class="p-3 mb-4 bg-danger text-white rounded shadow-sm">
        <h3 class="mb-3"><i class="bi bi-lightning-fill"></i> FLASH SALE!</h3>
        @isset($flashSaleEndDate)
        <p>Berakhir dalam: <strong id="countdown">00j 00m 00s</strong></p>
        @endisset
        
        <div class="row row-cols-2 row-cols-md-4 g-3">
            @foreach($flashSaleProducts as $product)
                {{-- Setiap card di sini sudah clickable karena kode di partials/product_card --}}
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
                {{-- Setiap card di sini sudah clickable --}}
                @include('partials.product_card', ['product' => $product]) 
            @endforeach
        </div>
        
    </div>
    @endif
    
    <hr class="my-5">
    
    {{-- ✨ PRODUK TERBARU --}}
    @if($newProducts->count())
    <div class="product-section mb-5">
        <h3 class="mb-3"><i class="bi bi-gem text-info me-2"></i> Produk Terbaru</h3>
        
        <div class="row row-cols-2 row-cols-md-4 g-3">
            @foreach($newProducts as $product)
                {{-- Setiap card di sini sudah clickable --}}
                @include('partials.product_card', ['product' => $product]) 
            @endforeach
        </div>
        
    </div>
    @endif
</div>
@endsection

@section('scripts')
{{-- Memperbaiki Hitungan Mundur (Countdown) Flash Sale --}}
@isset($flashSaleEndDate)
<script>
    const flashSaleEnd = new Date("{{ $flashSaleEndDate }}").getTime();
    const countdownElement = document.getElementById('countdown');

    // Cek apakah timestamp valid
    if (countdownElement && !isNaN(flashSaleEnd)) { 
        const updateCountdown = setInterval(function() {
            const now = new Date().getTime();
            const distance = flashSaleEnd - now;

            const hours = Math.floor((distance % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
            const minutes = Math.floor((distance % (1000 * 60 * 60)) / (1000 * 60));
            const seconds = Math.floor((distance % (1000 * 60)) / 1000);

            const formatTime = (t) => t.toString().padStart(2, '0');

            if (distance < 0) {
                clearInterval(updateCountdown);
                countdownElement.innerHTML = "SALE BERAKHIR";
            } else {
                countdownElement.innerHTML = 
                    formatTime(hours) + "j " + 
                    formatTime(minutes) + "m " + 
                    formatTime(seconds) + "s";
            }
        }, 1000);
    }
</script>
@endisset
@endsection