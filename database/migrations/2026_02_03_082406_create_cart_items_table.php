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
        Schema::create('cart_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('cart_id')
                ->constrained()
                ->cascadeOnDelete();
            $table->foreignId('product_id')
                ->constrained()
                ->cascadeOnDelete();
            $table->foreignId('product_color_id')
                ->constrained()
                ->cascadeOnDelete();
            $table->foreignId('product_size_id')
                ->constrained()
                ->cascadeOnDelete();
            $table->unsignedInteger('quantity');
            $table->decimal('price', 10, 2);
            $table->timestamps();

           $table->unique(
    ['cart_id', 'product_id', 'product_color_id', 'product_size_id'],
    'cart_items_unique'
);

        });
    }

    public function down(): void
    {
        Schema::dropIfExists('cart_items');
    }
};
