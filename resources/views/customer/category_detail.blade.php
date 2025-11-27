@extends('layouts.app')

@section('content')
<div class="container py-4">

    <div class="mb-4">
        <a href="{{ url()->previous() }}" class="btn btn-outline-secondary btn-sm mb-3">
            <i class="bi bi-arrow-left"></i> Kembali ke Menu Utama
        </a>
        <h1 class="border-bottom pb-2">{{ $category->name }}</h1>
        <p class="lead">Menampilkan semua produk dalam kategori {{ $category->name }}.</p>
    </div>

    <hr>

    {{-- 🏷️ KATEGORI LAINNYA (Tampilan Bulat) --}}
    @if($categories->count() > 1)
    <div class="category-section mb-5 shadow-sm p-3 rounded bg-white">
        <h3 class="mb-3 text-center"><i class="bi bi-tags-fill me-2"></i> Kategori Lain</h3>
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
    

    {{-- 🍔 DAFTAR PRODUK DALAM KATEGORI INI --}}
    @if($products->count())
    <div class="row row-cols-2 row-cols-md-4 g-3">
        @foreach($products as $product)
            @include('partials.product_card', ['product' => $product]) 
        @endforeach
    </div>
    @else
    <div class="alert alert-warning text-center">
        Tidak ada produk tersedia dalam kategori ini.
    </div>
    @endif

</div>
@endsection