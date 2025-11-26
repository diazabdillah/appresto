@extends('layouts.app')

@section('content')
<div class="container py-4">
    <h2><i class="bi bi-bag-check-fill me-2"></i> Riwayat Pesanan Saya</h2>
    <p class="text-muted">Menampilkan semua pesanan Delivery/Takeaway Anda yang sudah dibayar.</p>
{{-- NAVIGATION FILTER STATUS --}}
    <ul class="nav nav-tabs mb-4">
        <li class="nav-item">
            <a class="nav-link {{ $status === null || $status === 'paid' ? 'active bg-success text-white' : '' }}" 
               href="{{ route('app.orders.history') }}?status=paid">
               <i class="bi bi-check-circle-fill"></i> Sudah Dibayar
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link {{ $status === 'pending' ? 'active bg-warning text-dark' : '' }}" 
               href="{{ route('app.orders.history') }}?status=pending">
               <i class="bi bi-clock-fill"></i> Pending
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link {{ $status === 'failed' ? 'active bg-danger text-white' : '' }}" 
               href="{{ route('app.orders.history') }}?status=failed">
               <i class="bi bi-x-circle-fill"></i> Dibatalkan/Gagal
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link {{ $status === 'all' ? 'active bg-secondary text-white' : '' }}" 
               href="{{ route('app.orders.history') }}?status=all">
               <i class="bi bi-list-stars"></i> Semua Status
            </a>
        </li>
    </ul>
    
    <p class="text-muted">Menampilkan semua pesanan dengan status: <strong>{{ ucfirst($status ?? 'paid') }}</strong>.</p>
    @forelse ($history as $order)
        <div class="card mb-3 shadow-sm border-primary">
            <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
                <div>
                    <strong>Order #{{ $order->id }}</strong> 
                    <span class="badge bg-light text-dark">{{ strtoupper($order->order_type) }}</span>
                </div>
                <span>{{ $order->created_at->format('d M Y, H:i') }}</span>
            </div>
            
            <div class="card-body">
                <p>Status Pembayaran: <span class="badge bg-success">{{ ucfirst($order->payment_status) }}</span></p>
                
                @if($order->order_type == 'delivery')
                    <p class="small text-muted mb-2">Dikirim ke: {{ $order->delivery_address }}</p>
                @endif
                
                <ul class="list-group list-group-flush mb-3">
                    @foreach ($order->items as $item)
                        <li class="list-group-item d-flex justify-content-between">
                            <span>{{ $item->quantity }}x {{ $item->product->name ?? 'Produk Dihapus' }}</span>
                            <span>Rp {{ number_format($item->price * $item->quantity) }}</span>
                        </li>
                    @endforeach
                </ul>
                
                <div class="fw-bold fs-5 text-end text-danger">
                    Total Dibayar: Rp {{ number_format($order->total_amount) }}
                </div>
                <a href="{{ route('customer.order.detail', $order->id) }}" class="btn btn-sm btn-info text-white">
                <i class="bi bi-info-circle"></i> Detail Pesanan
            </a>
            </div>
        </div>
    @empty
        <div class="alert alert-info text-center">
            Anda belum memiliki riwayat pesanan yang berhasil dibayar.
        </div>
    @endforelse

    {{-- Tampilkan Paginasi --}}
    <div class="mt-4">
        {{ $history->links() }}
    </div>
</div>
@endsection