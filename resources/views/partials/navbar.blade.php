<nav class="navbar navbar-expand-lg navbar-dark bg-dark shadow-sm sticky-top">
    <div class="container">
        <a class="navbar-brand" href="{{ url('/') }}">
            <i class="bi bi-house-door-fill me-2"></i> RESTO-APP
        </a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav ms-auto">
                
                {{-- 1. LINK HOME --}}
                <li class="nav-item">
                    <a class="nav-link me-3" href="{{ url('/') }}">Home</a>
                </li>
                
                {{-- 2. LINK KERANJANG (DROPDOWN) --}}
                @if(session('table_id') || !empty(session('cart')))
                <li class="nav-item dropdown">
                    
                    <a class="nav-link btn btn-outline-warning text-white me-2 dropdown-toggle" 
                       href="#" 
                       id="cartDropdown" 
                       role="button" 
                       data-bs-toggle="dropdown" 
                       aria-expanded="false">
                        <i class="bi bi-cart-fill"></i> Keranjang (<span id="cart-count">{{ count(session('cart', [])) }}</span>)
                    </a>
                    
                    {{-- DROPDOWN MENU (Ringkasan Keranjang) --}}
                  <ul class="dropdown-menu dropdown-menu-end p-3 shadow" aria-labelledby="cartDropdown" style="min-width: 300px;">
    @php
        $navbarCart = session('cart', []);
        $navbarTotal = array_sum(array_map(fn($item) => $item['price'] * $item['quantity'], $navbarCart));
    @endphp

    @if(!empty($navbarCart))
        <h6 class="dropdown-header">Item di Keranjang:</h6>
        @foreach($navbarCart as $id => $item)
            @php $subtotal = $item['price'] * $item['quantity']; @endphp
            <li>
                <div class="dropdown-item p-1 small border-bottom mb-1">
                    <div class="d-flex justify-content-between mb-1">
                        <span>{{ Str::limit($item['name'], 25) }}</span>
                        <span class="fw-bold text-danger">Rp {{ number_format($subtotal) }}</span>
                    </div>
                    
                    {{-- ⭐ FORM + / - dan HAPUS Cepat ⭐ --}}
                    <div class="d-flex justify-content-between align-items-center">
                        <div class="input-group input-group-sm" style="width: 110px;">
                            {{-- Tombol Minus --}}
                            <button type="button" class="btn btn-outline-secondary btn-decrease" data-id="{{ $id }}" data-qty="{{ $item['quantity'] }}">
                                <i class="bi bi-dash"></i>
                            </button>
                            <input type="text" class="form-control text-center cart-qty-{{ $id }}" value="{{ $item['quantity'] }}" readonly>
                            {{-- Tombol Plus --}}
                            <button type="button" class="btn btn-outline-secondary btn-increase" data-id="{{ $id }}" data-qty="{{ $item['quantity'] }}">
                                <i class="bi bi-plus"></i>
                            </button>
                        </div>
                        
                        {{-- Tombol Hapus Cepat --}}
                        <a href="{{ route('customer.cart.remove', $id) }}" class="btn btn-sm btn-outline-danger" title="Hapus item">
                            <i class="bi bi-trash"></i> Hapus
                        </a>
                    </div>
                </div>
            </li>
        @endforeach
        
        <li><hr class="dropdown-divider"></li>
        <li>
            <span class="dropdown-item d-flex justify-content-between fw-bold" id="navbarTotalDisplay">
                <span>TOTAL:</span>
                <span class="text-danger">Rp {{ number_format($navbarTotal) }}</span>
            </span>
        </li>
        <li><hr class="dropdown-divider"></li>
        <li>
            <a class="btn btn-sm btn-primary w-100" href="{{ route('customer.cart.show') }}">
                Lihat Detail & Checkout
            </a>
        </li>
    @else
        <li><span class="dropdown-item text-muted text-center">Keranjang Anda kosong.</span></li>
    @endif
</ul>
                </li>
                @endif
                
                {{-- 3. OPSI KHUSUS PELANGGAN LOGIN (@auth) --}}
                @auth
                    
                    {{-- My Orders (Hanya untuk Customer Role) --}}
                    @if(Auth::user()->role == 'customer')
                        <li class="nav-item">
                            <a class="nav-link me-2" href="{{ route('app.orders.history') }}">
                                <i class="bi bi-receipt"></i> My Orders
                            </a>
                        </li>
                    @endif

                    {{-- Dropdown Akun (Nama User, Admin Dashboard, Logout) --}}
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                            <i class="bi bi-person-circle"></i> {{ Auth::user()->name }}
                        </a>
                        <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="navbarDropdown">
                            @if(Auth::user()->role == 'admin' || Auth::user()->role == 'staff')
                                <li><a class="dropdown-item" href="{{ route('admin.dashboard') }}">Dashboard Admin</a></li>
                                <li><hr class="dropdown-divider"></li>
                            @endif
                            <li>
                                <form method="POST" action="{{ route('logout') }}">
                                    @csrf
                                    <button class="dropdown-item" type="submit">Logout</button>
                                </form>
                            </li>
                        </ul>
                    </li>
                @else
                    {{-- 4. OPSI PUBLIK (@guest / @else) --}}
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('login') }}">Login</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link btn btn-primary text-white ms-lg-2" href="{{ route('register') }}">Daftar</a>
                    </li>
                @endauth
            </ul>
        </div>
    </div>
</nav>
<script>
// Pastikan script ini dimuat sekali di layouts/app.blade.php atau di sini.
document.addEventListener('DOMContentLoaded', function() {
    const totalDisplay = document.getElementById('navbarTotalDisplay');
    const cartCountDisplay = document.getElementById('cart-count');

    // Fungsi utama untuk mengirim permintaan AJAX
    function sendUpdate(productId, newQty) {
        if (newQty < 1) {
            // Untuk menghapus, gunakan link Hapus (simple GET/DELETE)
            window.location.href = `{{ url('customer/cart/remove') }}/${productId}`;
            return;
        }

        // Token CSRF dari meta tag
        const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
        const url = `{{ url('ajax/cart/update-qty') }}/${productId}`;

        fetch(url, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': csrfToken 
            },
            body: JSON.stringify({ quantity: newQty })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                // UPDATE VISUAL DISPLAY (Ini membutuhkan reload dropdown atau reload halaman)
                window.location.reload(); // Solusi paling sederhana untuk Blade/Session
            } else {
                alert('Gagal memperbarui: ' + data.message);
            }
        })
        .catch(error => {
            console.error('AJAX Error:', error);
            alert('Terjadi kesalahan koneksi.');
        });
    }

    // Listener untuk tombol INCREASE (+)
    document.querySelectorAll('.btn-increase').forEach(button => {
        button.addEventListener('click', function() {
            const id = this.dataset.id;
            const currentQty = parseInt(document.querySelector('.cart-qty-' + id).value);
            sendUpdate(id, currentQty + 1);
        });
    });

    // Listener untuk tombol DECREASE (-)
    document.querySelectorAll('.btn-decrease').forEach(button => {
        button.addEventListener('click', function() {
            const id = this.dataset.id;
            const currentQty = parseInt(document.querySelector('.cart-qty-' + id).value);
            // Cek jika Qty >= 1 sebelum kirim update. Jika 0, redirect ke hapus.
            if (currentQty > 1) {
                sendUpdate(id, currentQty - 1);
            } else {
                 // Jika Qty menjadi 0, kita redirect ke Cart Detail untuk konfirmasi Hapus
                 alert('Kuantitas harus diubah di halaman Keranjang Detail.');
                 window.location.href = `{{ route('customer.cart.show') }}`;
            }
        });
    });
});
</script>