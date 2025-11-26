@extends('layouts.app')

@section('content')
<div class="container py-4">
    <div class="row">
        
        {{-- Tombol Kembali --}}
        <div class="col-12 mb-3">
            <a href="{{ url()->previous() }}" class="btn btn-outline-secondary btn-sm">
                <i class="bi bi-arrow-left"></i> Kembali ke Menu
            </a>
        </div>

        {{-- Kolom Kiri: Gambar Produk (Slider) --}}
        <div class="col-md-5 mb-4">
            <div id="productImageCarousel" class="carousel slide border rounded shadow-sm" data-bs-ride="carousel">
                
                {{-- Indikator Carousel --}}
                <div class="carousel-indicators">
                    @foreach($product->images as $key => $image)
                        <button type="button" 
                                data-bs-target="#productImageCarousel" 
                                data-bs-slide-to="{{ $key }}" 
                                class="{{ $key == 0 ? 'active' : '' }}" 
                                aria-current="{{ $key == 0 ? 'true' : 'false' }}" 
                                aria-label="Slide {{ $key + 1 }}">
                        </button>
                    @endforeach
                </div>
                
                {{-- Isi Carousel (Gambar) --}}
                <div class="carousel-inner">
                    @forelse($product->images as $key => $image)
                        <div class="carousel-item {{ $key == 0 ? 'active' : '' }}">
                            <img src="{{ asset('storage/' . $image->path) }}" 
                                 class="d-block w-100" 
                                 alt="{{ $product->name }}" 
                                 style="height: 400px; object-fit: cover;">
                        </div>
                    @empty
                        <div class="carousel-item active">
                            <img src="{{ asset('images/placeholder.png') }}" 
                                 class="d-block w-100" 
                                 alt="Gambar Tidak Tersedia"
                                 style="height: 400px; object-fit: cover;">
                        </div>
                    @endforelse
                </div>

                {{-- Kontrol Next/Prev --}}
                @if($product->images->count() > 1)
                <button class="carousel-control-prev" type="button" data-bs-target="#productImageCarousel" data-bs-slide="prev">
                    <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                    <span class="visually-hidden">Previous</span>
                </button>
                <button class="carousel-control-next" type="button" data-bs-target="#productImageCarousel" data-bs-slide="next">
                    <span class="carousel-control-next-icon" aria-hidden="true"></span>
                    <span class="visually-hidden">Next</span>
                </button>
                @endif
            </div>
        </div>

        {{-- Kolom Kanan: Detail Produk & Aksi --}}
        <div class="col-md-7">
            <h1>{{ $product->name }}</h1>
            <p class="lead text-primary fw-bold display-6">
                {{ 'Rp ' . number_format($product->price, 0, ',', '.') }}
                @if($product->is_flash_sale)
                    <span class="badge bg-danger ms-2">FLASH SALE</span>
                @endif
            </p>

            <div class="mb-3">
                <span class="badge bg-secondary me-2">{{ $product->category->name }}</span>
                <span class="text-muted">Stok: {{ $product->stock }}</span>
            </div>

            {{-- Formulir Tambah ke Keranjang --}}
            <form action="{{ route('customer.cart.add', $product) }}" method="POST" class="d-flex mb-4">
                @csrf
                <input type="number" name="quantity" class="form-control me-2" value="1" min="1" max="{{ $product->stock }}" style="width: 100px;">
                <button type="submit" class="btn btn-success btn-lg">
                    <i class="bi bi-cart-plus"></i> Tambah ke Keranjang
                </button>
            </form>

            <hr>

            {{-- Deskripsi Produk --}}
            <h4 class="mt-4"><i class="bi bi-info-circle-fill me-2"></i> Deskripsi</h4>
            <div class="card card-body bg-light">
                <p>{{ $product->description ?? 'Deskripsi produk ini belum tersedia.' }}</p>
            </div>
        </div>
    </div>
    
    <hr class="my-5">

    {{-- Testimoni Produk --}}
    <div class="row">
        <div class="col-12">
            <h2><i class="bi bi-chat-left-text-fill me-2"></i> Testimoni Pelanggan ({{ $product->testimonies->count() }})</h2>
        </div>
        
        @forelse($product->testimonies as $testimony)
            <div class="col-md-6 mb-3">
                <div class="card shadow-sm">
                    <div class="card-body">
                        <h5 class="card-title">
                            {{ $testimony->user->name ?? 'Pelanggan Anonim' }} 
                            {{-- Tampilkan Rating Bintang --}}
                            <span class="float-end text-warning">
                                @for($i = 0; $i < 5; $i++)
                                    <i class="bi bi-star{{ $i < $testimony->rating ? '-fill' : '' }}"></i>
                                @endfor
                            </span>
                        </h5>
                        <p class="card-text">{{ $testimony->comment }}</p>
                        <small class="text-muted">
                            {{ $testimony->created_at->diffForHumans() }}
                        </small>
                    </div>
                </div>
            </div>
        @empty
            <div class="col-12">
                <div class="alert alert-info text-center">
                    Belum ada testimoni untuk produk ini.
                </div>
            </div>
        @endforelse
    </div>

</div>
@endsection