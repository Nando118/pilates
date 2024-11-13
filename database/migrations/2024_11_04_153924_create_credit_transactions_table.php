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
        Schema::create('credit_transactions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade'); // Hubungkan ke tabel users
            $table->enum('type', ['add', 'deduct', 'return']); // Menyimpan jenis transaksi (penambahan atau pengurangan)
            $table->integer('amount'); // Jumlah kredit yang ditambah atau dikurangi
            $table->string('transaction_code')->unique();
            $table->string('description')->nullable(); // Deskripsi atau keterangan transaksi
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('credit_transactions');
    }
};
