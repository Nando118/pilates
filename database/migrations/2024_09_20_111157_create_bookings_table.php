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
        Schema::create('bookings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('lesson_schedule_id')->constrained('lesson_schedules')->onDelete('cascade');
            $table->string('booked_by_name'); // Nama pengguna yang melakukan booking
            $table->foreignId('user_id')->nullable()->constrained('users')->onDelete('set null'); // ID pengguna jika sudah terdaftar
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('bookings');
    }
};
