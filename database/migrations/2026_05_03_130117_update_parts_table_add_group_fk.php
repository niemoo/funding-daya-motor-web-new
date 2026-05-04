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
        Schema::table('parts', function (Blueprint $table) {
            $table->dropColumn('group');
            $table->foreignId('part_group_id')
                ->nullable()
                ->after('deskripsi_part')
                ->constrained('part_groups')
                ->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('parts', function (Blueprint $table) {
            $table->dropForeign(['part_group_id']);
            $table->dropColumn('part_group_id');
            $table->string('group')->nullable();
        });
    }
};
