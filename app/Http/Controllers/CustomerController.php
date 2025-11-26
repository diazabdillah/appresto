<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Category;
use App\Models\Table;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Midtrans\Snap;
use Midtrans\Config;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;

class CustomerController extends Controller
{
  public function index(){
  return view('index');
  }
  public function showDeliveryMenu()
{
    // Query data menu sama persis seperti showMenu
    $categories = Category::with(['products' => function($query) {
         $query->where('stock', '>', 0)->orderBy('name', 'asc');
    }])->get();
    
    $flashSaleProducts = Product::where('is_flash_sale', true)
                                ->where('flash_sale_end_at', '>', Carbon::now())
                                ->where('stock', '>', 0)
                                ->take(8)
                                ->get();

    $newProducts = Product::where('stock', '>', 0)
                          ->latest('created_at')
                          ->take(8)
                          ->get();

    $bestSellerProducts = Product::where('is_best_seller', true)
                                 ->where('stock', '>', 0)
                                 ->take(8)
                                 ->get();
                                 
    // Halaman ini tidak perlu $table
    return view('customer.delivery_menu', compact(
        'categories', 
        'flashSaleProducts', 
        'newProducts', 
        'bestSellerProducts'
    ));
}
public function showMenu(Table $table)
{
    // 1. Setup Session
    session(['table_id' => $table->id]); 
    
    // 2. Query Data Menu
    $categories = Category::with(['products' => function($query) {
         // Pastikan produk diurutkan dan hanya yang memiliki stok > 0
         $query->where('stock', '>', 0)->orderBy('name', 'asc');
    }])->get();
    
    // Produk Flash Sale
    // Catatan: Query ini hanya untuk produk yang sedang Flash Sale
    $flashSaleProducts = Product::where('is_flash_sale', true)
                                ->where('flash_sale_end_at', '>', Carbon::now())
                                ->where('stock', '>', 0)
                                ->take(8)
                                ->get();

    // Produk Terbaru (Ambil 8 item terbaru)
    $newProducts = Product::where('stock', '>', 0)
                          ->latest('created_at')
                          ->take(8)
                          ->get();

    // Produk Best Seller (Ambil 8 item terlaris)
    $bestSellerProducts = Product::where('is_best_seller', true)
                                 ->where('stock', '>', 0)
                                 // ->orderBy('sales_count', 'desc') 
                                 ->take(8)
                                 ->get();
    
    // Mencari SATU produk yang dijadikan patokan waktu Flash Sale
    // Ambil yang pertama, yang memiliki waktu berakhir di masa depan
    $flashSaleItem = Product::where('is_flash_sale', true)
                            ->where('flash_sale_end_at', '>', Carbon::now())
                            ->first();

    // 2. Tentukan tanggal berakhirnya dengan pengecekan aman
    $flashSaleEndDate = null;

    // KOREKSI UTAMA: Gunakan operator nullsafe (?->) dan kolom yang benar (flash_sale_end_at)
    // Jika $flashSaleItem ada, ambil flash_sale_end_at. Jika itu Carbon object, format.
    if ($flashSaleItem) {
        $flashSaleEndDate = $flashSaleItem->flash_sale_end_at?->format('Y-m-d H:i:s');
    }
    
    // 3. Kirim SEMUA variabel ke view
    return view('customer.menu', compact(
        'categories', 
        'table', 
        'flashSaleProducts', 
        'flashSaleEndDate',
        'newProducts', 
        'bestSellerProducts'
    ));
}   
public function showCategoryDetail(Category $category)
{
    // Ambil semua produk yang terhubung dengan kategori ini dan memiliki stok > 0
    $products = $category->products()
                         ->where('stock', '>', 0)
                         ->orderBy('name', 'asc')
                         ->get();

    // Ambil semua kategori lainnya (untuk navigasi kartu bulat)
    $categories = Category::all();

    return view('customer.category_detail', compact('category', 'products', 'categories'));
}
public function showProductDetail(Product $product)
{
    // Eager load images dan testimoni untuk produk ini
    $product->load(['images', 'testimonies.user']); 
    
    // Anda bisa menambahkan logika produk terkait di sini jika diperlukan
    // $relatedProducts = Product::where('category_id', $product->category_id)->where('id', '!=', $product->id)->take(4)->get();

    return view('customer.product_detail', compact('product'));
}
    // Menambahkan item ke keranjang (Session)
    public function addToCart(Request $request)
    {
        $request->validate(['product_id' => 'required|exists:products,id', 'quantity' => 'required|integer|min:1']);
        
        $product = Product::find($request->product_id);
        $cart = session()->get('cart', []);
        $price = $product->discount_price ?? $product->price;

        // Logika penambahan/update item di $cart
        if(isset($cart[$product->id])) {
            $cart[$product->id]['quantity'] += $request->quantity;
        } else {
            $cart[$product->id] = [
                "product_id" => $product->id,
                "name" => $product->name,
                "quantity" => $request->quantity,
                "price" => $price,
            ];
        }

        session()->put('cart', $cart);
        return redirect()->back()->with('success', $product->name . ' ditambahkan ke keranjang.');
    }
    
    // Menampilkan halaman keranjang
   public function showCart()
{
    $cart = session()->get('cart', []);
    $total = array_sum(array_map(fn($item) => $item['price'] * $item['quantity'], $cart));

    // Tambahkan data meja yang tersedia jika user login (Asumsi: Staff/Admin)
    // Walaupun customer biasa tidak butuh ini, compact tidak akan crash.
    $availableTables = Table::where('status', 'available')->get();
    
    // Kirim $availableTables ke view
    return view('customer.cart', compact('cart', 'total', 'availableTables')); 
}
public function removeCartItem(Request $request, $productId)
    {
        // 1. Ambil array keranjang belanja dari session
        $cart = session()->get('cart', []); // Defaultkan ke array kosong jika belum ada

        // 2. Cek apakah produk dengan ID yang diberikan ada di keranjang
        if (isset($cart[$productId])) {
            
            // 3. Hapus item dari array
            unset($cart[$productId]); 

            // 4. Simpan kembali array keranjang yang telah diperbarui ke session
            session()->put('cart', $cart);

            // 5. Berikan respons sukses
            return redirect()->back()->with('success', 'Item berhasil dihapus dari keranjang.');
        }

        // Jika item tidak ditemukan
        return redirect()->back()->with('error', 'Item tidak ditemukan di keranjang.');
    }
public function updateCartQtyAjax(Request $request, $productId)
{
    $request->validate(['quantity' => 'required|integer']);

    $cart = session()->get('cart', []);
    $quantity = $request->quantity;

    if ($quantity > 0 && isset($cart[$productId])) {
        $cart[$productId]['quantity'] = $quantity;
        session()->put('cart', $cart);
        
        return response()->json([
            'success' => true,
            'message' => 'Kuantitas diperbarui.',
            'count' => count($cart),
            'total' => array_sum(array_map(fn($item) => $item['price'] * $item['quantity'], $cart))
        ]);
    } 
    
    // Jika kuantitas 0, kita biarkan logic hapus yang menanganinya, atau berikan error.
    return response()->json(['success' => false, 'message' => 'Gagal update kuantitas.'], 400);
}
    public function updateCart(Request $request)
{
    $cart = session()->get('cart', []);
    
    // Looping melalui item yang dikirim dari form (items[product_id][quantity])
    foreach ($request->input('items', []) as $productId => $data) {
        if (isset($cart[$productId]) && isset($data['quantity'])) {
            $quantity = (int)$data['quantity'];
            
            if ($quantity > 0) {
                // Update kuantitas jika > 0
                $cart[$productId]['quantity'] = $quantity;
            } else {
                // Hapus item jika kuantitas disetel ke 0
                unset($cart[$productId]); 
            }
        }
    }
    
    session()->put('cart', $cart); // Simpan kembali keranjang ke session

    return redirect()->route('customer.cart.show')->with('success', 'Keranjang berhasil diperbarui.');
}
    // Logika Checkout untuk Dine-in (QR Code)
    public function createOrderDineIn(Request $request)
    {
        $cart = session()->get('cart');
        $tableId = session()->get('table_id');
        
        if (empty($cart) || empty($tableId)) {
            return redirect()->route('customer.menu', $tableId ?? 1)->with('error', 'Keranjang atau Meja tidak valid.');
        }
        // Validasi input dari modal
        $request->validate([
            'customer_name' => 'required|string|max:255',
            'customer_email' => 'required|email|max:255',
            'customer_hp' => 'required|string|max:255',
        ]);
        // Panggil metode inti pemrosesan order
        return $this->processOrder($cart, [
            'order_type' => 'dine_in',
            'table_id' => $tableId,
            'user_id' => null,
            'delivery_address' => null,
            'customer_name' => $request->customer_name,
            'customer_hp' => $request->customer_hp,
            'customer_email' => $request->customer_email,
        ]);
    }

    // Logika Checkout untuk User Login (Delivery/Takeaway)
    public function createOrderAuthenticated(Request $request)
    {
        $request->validate([
            'order_type' => 'required|in:delivery,takeaway',
            'delivery_address' => 'required_if:order_type,delivery|nullable|string|max:255',
        ]);
        
        $cart = session()->get('cart');
        if (empty($cart)) {
            return redirect()->back()->with('error', 'Keranjang Anda kosong.');
        }

        // Panggil metode inti pemrosesan order
        return $this->processOrder($cart, [
            'order_type' => $request->order_type,
            'user_id' => Auth::id(),
            'table_id' => null,
            'delivery_address' => $request->delivery_address,
        ]);
    }
    
    // Metode Inti Pemrosesan Order dan Integrasi Midtrans
    protected function processOrder(array $cart, array $data)
    {
        try {
            DB::beginTransaction();
            $totalAmount = array_sum(array_map(fn($item) => $item['price'] * $item['quantity'], $cart));
            
            $order = Order::create(array_merge($data, ['total_amount' => $totalAmount, 'payment_status' => 'pending']));
            
            foreach ($cart as $item) {
                OrderItem::create(['order_id' => $order->id, 'product_id' => $item['product_id'], 'quantity' => $item['quantity'], 'price' => $item['price'],]);
            }
            
            Config::$serverKey = env('MIDTRANS_SERVER_KEY');
            Config::$isProduction = env('MIDTRANS_IS_PRODUCTION');
            
            $snapToken = Snap::getSnapToken(['transaction_details' => ['order_id' => (string) $order->id . '-' . time(), 'gross_amount' => $totalAmount,]]);
            
            $order->midtrans_snap_token = $snapToken;
            $order->save();
            session()->forget('cart'); 
            if ($data['order_type'] === 'dine_in') {
    session()->forget('table_id'); 
}

            DB::commit();

            return view('customer.payment', compact('order'));

        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->back()->with('error', 'Gagal membuat pesanan: ' . $e->getMessage());
        }
    }
    
    // Menampilkan Histori Order Pribadi (user yang login)
   // app/Http/Controllers/CustomerController.php (Direvisi)

public function showMyOrders(Request $request) // Menerima Request
{
    if (!Auth::check()) {
        return redirect()->route('login');
    }
    
    $query = Order::where('user_id', Auth::id())
                  ->with('items.product')
                  ->latest();

    // Logika Filter Berdasarkan Query String (?status=...)
    $status = $request->get('status');
    if ($status && in_array($status, ['paid', 'pending', 'failed'])) {
        $query->where('payment_status', $status);
    } elseif ($status === 'all') {
        // Tampilkan semua jika status=all
    } else {
        // Default: Hanya tampilkan yang berhasil dibayar
        $query->whereIn('payment_status', ['paid', 'settlement']); 
    }

    $history = $query->paginate(10)->withQueryString(); // withQueryString agar paginasi tidak menghilangkan filter
                    
    return view('customer.my_orders', compact('history', 'status')); // Kirim status saat ini ke view
}
    public function showOrderDetail(Order $order)
{
    // Cek otorisasi: hanya pemilik order (user login) atau order dine-in yang bisa melihat.
    if (Auth::check() && $order->user_id !== Auth::id()) {
        // Jika login tapi bukan pemilik
        return redirect()->route('app.orders.history')->with('error', 'Akses ke pesanan ini ditolak.');
    } elseif (!Auth::check() && $order->order_type !== 'dine_in') {
        // Jika tidak login, hanya boleh melihat order dine-in (melalui QR/history meja)
        return redirect('/')->with('error', 'Akses ditolak. Silakan login atau scan QR.');
    }

    // Eager load items dan product jika belum dilakukan
    $order->load('items.product');

    return view('customer.order_detail', compact('order'));
}
public function cancelOrder(Order $order)
    {
        // 1. Otorisasi
        // Hanya pemilik order (jika login) atau order dine-in yang boleh membatalkan
        if (Auth::check() && $order->user_id !== Auth::id()) {
             return redirect()->back()->with('error', 'Anda tidak memiliki izin membatalkan order ini.');
        }
        
        // 2. Cek Status
        if ($order->payment_status !== 'pending') {
            return redirect()->back()->with('error', 'Pesanan ini sudah diproses atau sudah dibayar dan tidak dapat dibatalkan.');
        }

        DB::beginTransaction();
        try {
            // 3. Kembalikan Stok Produk
            foreach ($order->items as $item) {
                $product = $item->product;
                if ($product) {
                    $product->stock += $item->quantity; // Tambahkan kembali stok
                    $product->save();
                }
            }

            // 4. Update Status Order
            $order->payment_status = 'failed'; // Mengubah status menjadi gagal/dibatalkan
            $order->save();
            
            DB::commit();
            return redirect()->back()->with('success', 'Pesanan #'.$order->id.' berhasil dibatalkan dan stok telah dikembalikan.');

        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->back()->with('error', 'Gagal membatalkan pesanan: ' . $e->getMessage());
        }
    }
    // Menampilkan Histori Order Meja (tanpa login)
    public function showTableHistory()
    {
        $tableId = session('table_id'); 
        if (empty($tableId)) {
            return redirect('/')->with('error', 'Silakan scan QR code meja terlebih dahulu.');
        }
        $history = Order::where('table_id', $tableId)
                        ->whereIn('payment_status', ['paid', 'settlement']) 
                        ->with('items.product')
                        ->latest()
                        ->paginate(10); 
        return view('customer.table_history', compact('history'));
    }
}