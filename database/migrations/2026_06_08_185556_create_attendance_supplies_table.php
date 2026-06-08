<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('attendance_supplies', function (Blueprint $table) {
            $table->id();
            $table->foreignId('attendance_id')->constrained('attendances')->cascadeOnDelete();
            $table->string('kode_part');
            $table->integer('quantity_requested');
            $table->integer('quantity_supplied')->default(0);
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->index(['attendance_id', 'kode_part']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('attendance_supplies');
    }
};
