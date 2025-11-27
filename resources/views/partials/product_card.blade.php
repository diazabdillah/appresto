<div class="col">
    <div class="card h-100 shadow-sm">
        {{-- Gambar Produk (Contoh) --}}
        <img src="..." class="card-img-top" alt="{{ $product->name }}" style="height: 150px; object-fit: cover;">
        <div class="card-body d-flex flex-column">
            <h5 class="card-title">{{ $product->name }}</h5>
            
            {{-- Tampilan Harga (Diskon vs. Normal) --}}
            @if($product->discount_price)
                <p class="mb-1">
                    <s class="text-danger small">Rp {{ number_format($product->price) }}</s>
                    <strong class="text-success ms-2">Rp {{ number_format($product->discount_price) }}</strong>
                </p>
            @else
                <p class="mb-1"><strong class="text-primary">Rp {{ number_format($product->price) }}</strong></p>
            @endif

            {{-- Form Tambah ke Keranjang --}}
            <form action="{{ route('customer.cart.add', $product->id) }}" method="POST" class="mt-auto pt-2">
                @csrf
                <input type="hidden" name="product_id" value="{{ $product->id }}">
                <div class="input-group input-group-sm">
                    <input type="number" name="quantity" value="1" min="1" class="form-control" style="max-width: 60px;">
                    <button type="submit" class="btn btn-primary"><i class="bi bi-cart-plus"></i> Pesan</button>
                </div>
            </form>
        </div>
    </div>
</div>