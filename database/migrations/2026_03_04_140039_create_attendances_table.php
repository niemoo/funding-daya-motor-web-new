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
        Schema::create('attendances', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->date('attendance_date');

            // === Check-in ===
            $table->timestamp('checkin_time');
            $table->string('checkin_latitude');
            $table->string('checkin_longitude');
            $table->text('checkin_photo');

            // === Data Toko / Penanggung Jawab ===
            $table->string('store_name');
            $table->string('person_in_charge_name');
            $table->string('person_in_charge_phone');

             // === Check-out ===
            $table->timestamp('checkout_time')->nullable();
            $table->string('checkout_latitude')->nullable();
            $table->string('checkout_longitude')->nullable();
            $table->text('checkout_photo')->nullable();

            // === Durasi kerja (menit) ===
            $table->integer('work_duration_minutes')->nullable();

            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('attendances');
    }
};
