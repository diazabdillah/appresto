<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
      Schema::create('product_images', function (Blueprint $table) {
            $table->id();

            // Kunci Asing ke Produk (Wajib)
            // Gambar harus terhubung ke satu produk
            $table->foreignId('product_id')
                  ->constrained() // Mengacu pada tabel 'products'
                  ->onDelete('cascade'); // Jika produk dihapus, semua gambarnya juga dihapus

            $table->string('path'); // Path atau URL ke file gambar
            $table->boolean('is_main')->default(false); // Opsional: untuk menandai gambar utama

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('product_images');
    }
};
