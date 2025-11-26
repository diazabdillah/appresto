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
       Schema::create('testimonies', function (Blueprint $table) {
            $table->id();

            // Kunci Asing ke Produk (Wajib)
            // Menggunakan constrained() untuk membuat foreign key dan index
            $table->foreignId('product_id')->constrained()->onDelete('cascade');

            // Kunci Asing ke User (Opsional, jika testimoni hanya bisa diberikan user yang login)
            // Jika Anda ingin mengizinkan testimoni anonim, ubah menjadi nullable()
            $table->foreignId('user_id')->nullable()->constrained()->onDelete('set null'); 

            $table->tinyInteger('rating')->unsigned()->default(5); // Rating 1-5
            $table->text('comment');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('testimonies');
    }
};
