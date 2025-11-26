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
            @foreach($categories as $cat)
                <div class="text-center mx-2 flex-shrink-0" style="width: 100px;">
                    {{-- LINK KE HALAMAN DETAIL KATEGORI LAIN --}}
                    <a href="{{ route('customer.category.show', $cat) }}" class="text-decoration-none text-dark d-block">
                        <div class="rounded-circle d-flex align-items-center justify-content-center mx-auto mb-1" 
                             style="width: 70px; height: 70px; background-color: #f8f9fa; border: 2px solid {{ $cat->id == $category->id ? '#0d6efd' : '#ced4da' }};">
                            
                            {{-- Placeholder untuk Icon/Gambar Kategori --}}
                            <i class="bi bi-{{ strtolower(substr($cat->name, 0, 1)) }}-circle-fill {{ $cat->id == $category->id ? 'text-primary' : 'text-secondary' }}" style="font-size: 2rem;"></i>
                        </div>
                        <small class="fw-bold">{{ $cat->name }}</small>
                    </a>
                </div>
            @endforeach
        </div>
    </div>
    @endif
    
    <hr class="my-5">

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