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
        Schema::create('category_matches', function (Blueprint $table) {
    $table->id();

    $table->foreignId('category_id')
        ->constrained()
        ->cascadeOnDelete();

    $table->foreignId('matched_category_id')
        ->constrained('categories')
        ->cascadeOnDelete();

    $table->timestamps();

    $table->unique([
        'category_id',
        'matched_category_id'
    ]);
});
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('category_matches');
    }
};
