<nav class="navbar navbar-expand-lg navbar-dark bg-dark shadow-sm sticky-top">
    <div class="container">
        <a class="navbar-brand" href="/">
            <i class="bi bi-house-door-fill me-2"></i> RESTO-APP
        </a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav ms-auto">
                
                {{-- ⭐ LINK HOME (BARU) ⭐ --}}
                <li class="nav-item">
                    <a class="nav-link me-3" href="{{ url('/') }}">
                        Home
                    </a>
                </li>
                
                {{-- Tampilkan Link Keranjang --}}
                @if(session('table_id') || !empty(session('cart')))
                <li class="nav-item">
                    <a class="nav-link btn btn-outline-warning text-white me-2" href="{{ route('customer.cart.show') }}">
                        <i class="bi bi-cart-fill"></i> Keranjang (<span id="cart-count">{{ count(session('cart', [])) }}</span>)
                    </a>
                </li>
                @endif

                @auth
                    {{-- Link My Orders (Tambahan) --}}
                    @if(Auth::user()->role == 'customer')
                        <li class="nav-item">
                            <a class="nav-link me-2" href="{{ route('app.orders.history') }}">
                                <i class="bi bi-receipt"></i> My Orders
                            </a>
                        </li>
                    @endif

                    {{-- Dropdown untuk User yang Login --}}
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                            <i class="bi bi-person-circle"></i> {{ Auth::user()->name }}
                        </a>
                        <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="navbarDropdown">
                            @if(Auth::user()->role == 'admin' || Auth::user()->role == 'staff')
                                <li><a class="dropdown-item" href="{{ route('admin.dashboard') }}">Dashboard Admin</a></li>
                                <li><hr class="dropdown-divider"></li>
                            @endif
                            {{-- Link Logout --}}
                            <li>
                                <form method="POST" action="{{ route('logout') }}">
                                    @csrf
                                    <button class="dropdown-item" type="submit">Logout</button>
                                </form>
                            </li>
                        </ul>
                    </li>
                @else
                    {{-- Link Login/Register untuk User Publik --}}
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