<?php

namespace App\Http\Controllers;

use App\Models\Order;
use Illuminate\Http\Request;
use Midtrans\Config;
use Midtrans\Notification;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB; // Diperlukan untuk transaksi

class MidtransController extends Controller
{
    /**
     * Menerima notifikasi status pembayaran dari Midtrans.
     */
    public function notificationHandler(Request $request)
    {
        // 1. Setup Midtrans menggunakan variabel ENV
        Config::$isProduction = env('MIDTRANS_IS_PRODUCTION');
        Config::$serverKey = env('MIDTRANS_SERVER_KEY');
        Config::$merchantId = env('MIDTRANS_MERCHANT_ID'); 
        
        try {
            $notification = new Notification();
        } catch (\Exception $e) {
            Log::error("Midtrans Notification Error: " . $e->getMessage());
            return response()->json(['message' => 'Invalid notification structure'], 400);
        }
        
        $orderIdFull = $notification->order_id; 
        $orderId = explode('-', $orderIdFull)[0];

        $transactionStatus = $notification->transaction_status;
        $fraudStatus = $notification->fraud_status;

        $order = Order::with('items.product')->find($orderId); // Eager load items dan product

        if (!$order) {
            Log::warning("Midtrans Notification: Order ID not found in database: " . $orderId);
            return response()->json(['message' => 'Order not found'], 404);
        }

        // Gunakan transaksi DB untuk memastikan operasi stok atomik
        DB::beginTransaction();
        
        try {
            // 2. Logika Update Status Order
            if ($transactionStatus == 'capture' && $fraudStatus == 'accept' || $transactionStatus == 'settlement') 
            {
                // HANYA UPDATE JIKA BELUM DIBAYAR (untuk menghindari double processing)
                if ($order->payment_status != 'paid') {
                    $order->payment_status = 'paid';
                    
                    // --- LOGIKA STOK DAN PENJUALAN ---
                    foreach ($order->items as $item) {
                        $product = $item->product;
                        if ($product) {
                            $product->sales_count += $item->quantity; 
                            $product->stock -= $item->quantity; // Kurangi Stok
                            $product->save();
                        }
                    }
                    // --- END LOGIKA STOK ---
                }
            } elseif ($transactionStatus == 'pending') {
                $order->payment_status = 'pending';
            } elseif (in_array($transactionStatus, ['deny', 'cancel', 'expire'])) {
                $order->payment_status = 'failed';
            }

            $order->save();
            DB::commit();
            
        } catch (\Exception $e) {
            DB::rollback();
            Log::error("Midtrans Stock Update Failed for Order ID " . $orderId . ": " . $e->getMessage());
            return response()->json(['message' => 'Internal Server Error during processing'], 500);
        }
        
        // 3. Beri respons sukses ke Midtrans
        return response()->json(['message' => 'Notification processed successfully']);
    }
}