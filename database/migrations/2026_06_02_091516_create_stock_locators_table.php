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
        Schema::create('stock_locators', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('branch_id');
            $table->string('kode_part');
            $table->unsignedBigInteger('part_group_id')->nullable();
            $table->string('lokasi_stock')->nullable();
            $table->decimal('jumlah', 10, 2)->default(0);
            $table->decimal('nilai_stock', 15, 2)->default(0);
            $table->timestamps();
            $table->softDeletes();

            $table->index('kode_part');
            $table->index('branch_id');
            $table->index('part_group_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('stock_locators');
    }
};
