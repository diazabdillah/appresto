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
                {{-- ⭐ PERIKSA AKSES: HARUS LOGIN ATAU PUNYA ID MEJA ⭐ --}}
                @if (Auth::check() || session('table_id'))

                    <form id="mainCheckoutForm" action="" method="POST">
                        @csrf

                        {{-- 1. PILIHAN MEJA & TIPE PESANAN --}}
                        <div class="mb-3">
                            <label class="form-label">Tipe Pesanan:</label>
                            <select name="order_type" class="form-select" id="orderTypeSelect" required>
                                <option value="">-- Pilih Tipe Pesanan --</option>
                                
                                {{-- Opsi DINE-IN (Scan QR) --}}
                                @if(session('table_id'))
                                    <option value="dine_in_scanned" selected>Dine-In (Meja {{ session('table_id') }} - SCAN)</option>
                                @endif
                                
                                {{-- Opsi DINE-IN (Dropdown Manual - Hanya muncul jika TIDAK ADA scan ID) --}}
                                @if(!session('table_id') && Auth::check() && $availableTables->count())
                                    <option value="dine_in_manual">Dine-In (Pilih Meja)</option>
                                @endif
                                
                                {{-- Opsi Takeaway/Delivery (Hanya jika Login) --}}
                                @auth
                                    <option value="takeaway">Takeaway (Ambil di Restoran)</option>
                                    <option value="delivery">Delivery (Diantar ke Alamat)</option>
                                @endauth
                            </select>
                        </div>
                        
                        {{-- 2. DROPDOWN MEJA TERSEDIA (Kondisional untuk Staff Manual Order) --}}
                        <div class="mb-3" id="tableSelectField" style="display:none;">
                            <label for="manualTableId" class="form-label">Pilih Meja Tersedia:</label>
                            <select name="table_id_manual" class="form-select" id="manualTableId">
                                <option value="">-- Pilih Meja --</option>
                                @foreach($availableTables as $table)
                                    <option value="{{ $table->id }}">Meja {{ $table->table_number }}</option>
                                @endforeach
                            </select>
                        </div>

                        {{-- Input Hidden untuk Meja yang di-Scan --}}
                        @if(session('table_id'))
                            <input type="hidden" name="table_id_scanned" value="{{ session('table_id') }}">
                        @endif
                        
                        {{-- 3. INPUT KONTAK --}}
                        <div class="mb-3">
                            <label for="customer_name" class="form-label">Nama Anda (Wajib)</label>
                            <input type="text" name="customer_name" id="customer_name" class="form-control" required placeholder="Nama pemesan">
                        </div>
            <div class="mb-3">

<label for="customer_email" class="form-label">Email (Opsional)</label>

<input type="email" name="customer_email" id="customer_email" class="form-control" placeholder="Email untuk tanda terima">

</div>

<div class="mb-3">

<label for="customer_hp" class="form-label">Nomor HP (Wajib)</label>

<input type="text" name="customer_hp" id="customer_hp" class="form-control" required placeholder="Nomor HP aktif">

</div>
                        
                        {{-- 4. INPUT ALAMAT (Kondisional untuk Delivery) --}}
                        <div class="mb-3" id="deliveryAddressField" style="display:none;">
                            <label for="deliveryAddress" class="form-label">Alamat Pengiriman (Wajib untuk Delivery):</label>
                            <textarea name="delivery_address" id="deliveryAddress" class="form-control" rows="3"></textarea>
                        </div>

                        <hr>
                        
                        <button type="submit" id="submitCheckout" class="btn btn-primary w-100 mt-2">
                            <i class="bi bi-credit-card-fill me-2"></i> Lanjutkan Pembayaran
                        </button>
                    </form>

                @else
                    {{-- JIKA TIDAK ADA AKSES --}}
                    <div class="alert alert-danger text-center">
                        <i class="bi bi-person-fill-lock"></i> **Akses Dibatasi.**
                        <br>Silakan <a href="{{ route('login') }}" class="alert-link">Login</a> atau Scan QR Meja.
                    </div>
                @endif
                
           
            {{-- Footer Error/Login (Sama seperti sebelumnya) --}}
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const mainForm = document.getElementById('mainCheckoutForm');
    const orderTypeSelect = document.getElementById('orderTypeSelect');
    const deliveryAddressField = document.getElementById('deliveryAddressField');
    const deliveryAddressTextarea = document.getElementById('deliveryAddress');
    const tableSelectField = document.getElementById('tableSelectField'); // Field baru
    const manualTableId = document.getElementById('manualTableId');      // Select meja baru
    const submitButton = document.getElementById('submitCheckout');

    const dineInRoute = '{{ session("table_id") ? route("customer.order.create.dinein") : "" }}'; // Route untuk Scanned ID
    const authRoute = '{{ Auth::check() ? route("app.order.create.auth") : "" }}'; // Route untuk Manual/Delivery/Takeaway

    function updateFormLogic() {
        if (!orderTypeSelect) { // Jika hanya ada mode Dine-in (Scanned)
             mainForm.action = dineInRoute;
             submitButton.removeAttribute('disabled');
             return;
        }
        
        const selectedType = orderTypeSelect.value;

        // Reset state
        deliveryAddressField.style.display = 'none';
        deliveryAddressTextarea.removeAttribute('required');
        tableSelectField.style.display = 'none';
        manualTableId.removeAttribute('required');
        mainForm.action = ''; // Reset action
        submitButton.setAttribute('disabled', 'disabled');

        // 1. Logika Pengaturan Form
        if (selectedType === 'dine_in_manual') {
            mainForm.action = authRoute;
            tableSelectField.style.display = 'block';
            manualTableId.setAttribute('required', 'required'); // Wajib pilih meja
            
        } else if (selectedType === 'dine_in_scanned') {
            mainForm.action = dineInRoute;
            
        } else if (selectedType === 'delivery') {
            mainForm.action = authRoute;
            deliveryAddressField.style.display = 'block';
            deliveryAddressTextarea.setAttribute('required', 'required');
            
        } else if (selectedType === 'takeaway') {
            mainForm.action = authRoute;
        }

        // 2. VALIDASI AKHIR
        if (mainForm.action) {
            // Validasi tambahan untuk Dine-In Manual (harus pilih meja)
            if (selectedType === 'dine_in_manual' && !manualTableId.value) {
                submitButton.setAttribute('disabled', 'disabled');
            } else {
                submitButton.removeAttribute('disabled');
            }
        }
    }

    if (orderTypeSelect) {
        orderTypeSelect.addEventListener('change', updateFormLogic);
        updateFormLogic();
    }
    
    if (manualTableId) {
        manualTableId.addEventListener('change', updateFormLogic);
    }
});
</script>