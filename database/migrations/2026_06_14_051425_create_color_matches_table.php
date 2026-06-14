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
       Schema::create('color_matches', function (Blueprint $table) {
    $table->id();

    $table->foreignId('color_id')
        ->constrained()
        ->cascadeOnDelete();

    $table->foreignId('matched_color_id')
        ->constrained('colors')
        ->cascadeOnDelete();

    $table->timestamps();

    $table->unique([
        'color_id',
        'matched_color_id'
    ]);
});
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('color_matches');
    }
};
