@extends('layouts.app')

@section('content')
<div class="container py-4">
    <h2><i class="bi bi-clock-history"></i> Riwayat Pembayaran Meja {{ session('table_id') }}</h2>
    <p class="text-muted">Riwayat pesanan yang sudah lunas dibayar dari meja ini.</p>

    @forelse ($history as $order)
        <div class="card mb-3 shadow-sm border-success">
            <div class="card-header bg-success text-white d-flex justify-content-between">
                <strong>Pesanan #{{ $order->id }}</strong> 
                <span>{{ $order->created_at->format('d M Y, H:i') }}</span>
            </div>
            <div class="card-body">
                <ul class="list-unstyled">
                    @foreach ($order->items as $item)
                        <li>{{ $item->quantity }}x {{ $item->product->name ?? 'Produk Dihapus' }} 
                            <span class="float-end">Rp {{ number_format($item->price * $item->quantity) }}</span>
                        </li>
                    @endforeach
                </ul>
                <hr>
                <div class="fw-bold fs-5 text-end">
                    Total: Rp {{ number_format($order->total_amount) }}
                </div>
            </div>
        </div>
    @empty
        <div class="alert alert-info text-center">Belum ada riwayat pembayaran yang tercatat.</div>
    @endforelse

    {{ $history->links() }}
</div>
@endsection