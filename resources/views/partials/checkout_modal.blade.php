<div class="modal fade" id="checkoutModal" tabindex="-1" aria-labelledby="checkoutModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-success text-white">
                <h5 class="modal-title" id="checkoutModalLabel"><i class="bi bi-wallet2 me-2"></i> Konfirmasi Pembayaran</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                
                {{-- 💰 RINGKASAN PESANAN (BARU) 💰 --}}
                @php
                    $modalCart = session('cart', []);
                    $modalTotal = array_sum(array_map(fn($item) => $item['price'] * $item['quantity'], $modalCart));
                @endphp
                
                <div class="p-3 mb-4 bg-light rounded border">
                    <h6>Ringkasan Order:</h6>
                    <ul class="list-unstyled small mb-2">
                        @foreach($modalCart as $item)
                            <li>{{ $item['quantity'] }}x {{ $item['name'] }} <span class="float-end">Rp {{ number_format($item['price'] * $item['quantity']) }}</span></li>
                        @endforeach
                    </ul>
                    <div class="fw-bold fs-5 border-top pt-2 d-flex justify-content-between">
                        <span>TOTAL AKHIR:</span>
                        <span class="text-danger">Rp {{ number_format($modalTotal) }}</span>
                    </div>
                </div>
                
                {{-- 📍 Skenario 1: DINE-IN (Selalu diprioritaskan jika ada ID Meja) --}}
                @if(session('table_id'))
                    <h6 class="text-center mb-4 text-success">
                        Pesanan Meja No. {{ session('table_id') }}
                        <br><small class="text-muted">Masukkan detail kontak untuk kemudahan staf dan notifikasi.</small>
                    </h6>
                    
                    <form action="{{ route('customer.order.create.dinein') }}" method="POST">
                        @csrf
                        
                        {{-- Input Kontak Dine-in --}}
                        <div class="mb-3">
                            <label for="customer_name" class="form-label">Nama Anda (Wajib)</label>
                            <input type="text" name="customer_name" id="customer_name" class="form-control" required placeholder="Nama pemesan">
                        </div>
                        <div class="mb-3">
                            <label for="customer_email" class="form-label">Email </label>
                            <input type="email" name="customer_email" id="customer_email" class="form-control" placeholder="Email untuk tanda terima / kontak">
                        </div>
                          <div class="mb-3">
                            <label for="customer_email" class="form-label">Nomer HP</label>
                            <input type="text" name="customer_hp" id="customer_hp" class="form-control" placeholder="Email untuk tanda terima / kontak">
                        </div>
                        <hr>
                        
                        <button type="submit" class="btn btn-success w-100 mt-2">
                            <i class="bi bi-credit-card-fill me-2"></i> Lanjutkan Pembayaran Midtrans
                        </button>
                    </form>
                    
                @else
                    {{-- Skenario 2: DELIVERY/TAKEAWAY & TANPA MEJA --}}
                    
                    @auth
                        {{-- Opsi DELIVERY/TAKEAWAY (User Login) --}}
                        <h6 class="mt-3 text-warning"><i class="bi bi-geo-alt-fill"></i> Selesaikan Pesanan Delivery / Takeaway:</h6>
                        <form action="{{ route('app.order.create.auth') }}" method="POST">
                            @csrf
                            <p class="text-muted small">Anda login sebagai **{{ Auth::user()->name }}**.</p>
                            
                            {{-- Input Tipe Pesanan dan Alamat --}}
                            <div class="mb-3">
                                <label class="form-label">Tipe Pesanan:</label>
                                <select name="order_type" class="form-select" id="orderTypeSelect" required>
                                    <option value="takeaway">Takeaway (Ambil di Restoran)</option>
                                    <option value="delivery">Delivery (Diantar ke Alamat)</option>
                                </select>
                            </div>
                            
                            <div class="mb-3" id="deliveryAddressField" style="display:none;">
                                <label for="deliveryAddress" class="form-label">Alamat Pengiriman:</label>
                                <textarea name="delivery_address" id="deliveryAddress" class="form-control" rows="3"></textarea>
                            </div>

                            <button type="submit" class="btn btn-primary w-100 mt-2">
                                Lanjutkan Pembayaran Akun
                            </button>
                        </form>
                    @else
                        {{-- ERROR / TIDAK ADA MEJA & TIDAK LOGIN --}}
                        <div class="alert alert-danger text-center">
                             <i class="bi bi-x-octagon-fill"></i> Keranjang tidak terasosiasi ke Meja.
                             <br>Silakan <a href="{{ route('login') }}" class="alert-link">Login</a> untuk Delivery/Takeaway.
                        </div>
                    @endauth
                @endif
                
            </div>
        </div>
    </div>
</div>

<script>
// Logic JavaScript (Sama seperti sebelumnya)
document.addEventListener('DOMContentLoaded', function () {
    const orderTypeSelect = document.getElementById('orderTypeSelect');
    const deliveryAddressField = document.getElementById('deliveryAddressField');

    if (orderTypeSelect) {
        function toggleDeliveryField() {
             if (orderTypeSelect.value === 'delivery') {
                deliveryAddressField.style.display = 'block';
                deliveryAddressField.querySelector('textarea').setAttribute('required', 'required');
            } else {
                deliveryAddressField.style.display = 'none';
                deliveryAddressField.querySelector('textarea').removeAttribute('required');
            }
        }
        orderTypeSelect.addEventListener('change', toggleDeliveryField);
        toggleDeliveryField();
    }
});
</script>