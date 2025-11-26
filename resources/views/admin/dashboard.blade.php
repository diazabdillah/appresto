@extends('layouts.app')

@section('content')
<div class="container py-4">
    <div class="d-flex justify-content-between align-items-center mb-4 border-bottom pb-2">
        <h2><i class="bi bi-speedometer2 me-2"></i> Dashboard Staff</h2>
        <div>
            <a href="{{ route('admin.tables.index') }}" class="btn btn-secondary me-2">Kelola Meja</a>
            <a href="#" class="btn btn-primary">Kelola Menu</a>
        </div>
    </div>

    <div class="row mb-5">
        <div class="col-md-4">
            <div class="card bg-info text-white shadow">
                <div class="card-body">
                    <h5 class="card-title">Pesanan Baru</h5>
                    <p class="card-text display-4">{{ $newOrderCount ?? '0' }}</p>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card bg-warning text-dark shadow">
                <div class="card-body">
                    <h5 class="card-title">Siap Kirim/Saji</h5>
                    <p class="card-text display-4">{{ $readyOrderCount ?? '0' }}</p>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card bg-success text-white shadow">
                <div class="card-body">
                    <h5 class="card-title">Total Pendapatan Hari Ini</h5>
                    <p class="card-text display-6">Rp {{ number_format($dailyRevenue ?? 0) }}</p>
                </div>
            </div>
        </div>
    </div>

    <h3><i class="bi bi-basket3-fill"></i> Pesanan Masuk (Paid)</h3>
    <table class="table table-striped table-hover">
        <thead>
            <tr>
                <th>ID Order</th>
                <th>Tipe</th>
                <th>Lokasi</th>
                <th>Total</th>
                <th>Waktu</th>
                <th>Aksi</th>
            </tr>
        </thead>
        <tbody>
            @forelse($pendingOrders as $order)
            <tr>
                <td>#{{ $order->id }}</td>
                <td><span class="badge bg-{{ $order->order_type == 'dine_in' ? 'primary' : 'danger' }}">{{ strtoupper($order->order_type) }}</span></td>
                <td>{{ $order->order_type == 'dine_in' ? 'Meja ' . $order->table->table_number : ($order->user->name ?? 'User Delivery') }}</td>
                <td>Rp {{ number_format($order->total_amount) }}</td>
                <td>{{ $order->created_at->diffForHumans() }}</td>
                <td>
                    <a href="#" class="btn btn-sm btn-info text-white">Lihat Detail</a>
                    <button class="btn btn-sm btn-success">Selesai</button>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="6" class="text-center text-muted">Tidak ada pesanan baru yang menunggu.</td>
            </tr>
            @endforelse
        </tbody>
    </table>
</div>
@endsection