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
        Schema::table('stock_locators', function (Blueprint $table) {
            $table->bigInteger('nilai_stock')->default(0)->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('stock_locators', function (Blueprint $table) {
            $table->decimal('nilai_stock', 15, 2)->default(0)->change();
        });
    }
};
