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
        Schema::create('lesson_schedules', function (Blueprint $table) {
            $table->id();
            $table->date('date')->nullable(false); // Kolom untuk menyimpan tanggal
            $table->foreignId('time_slot_id')->constrained()->onDelete('cascade');  // Foreign key ke tabel time_slots
            $table->foreignId('lesson_id')->constrained()->onDelete('cascade');     // Foreign key ke tabel lessons
            $table->foreignId('lesson_type_id')->constrained()->onDelete('cascade'); // Foreign key ke tabel lesson_types
            $table->foreignId('user_id')->constrained()->onDelete('cascade');        // Foreign key ke tabel users (misalnya untuk trainer/coach)            
            $table->integer('quota')->default(0); // Kolom quota
            $table->integer('credit_price')->default(0); // Kolom quota            
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('lesson_schedules');
    }
};
