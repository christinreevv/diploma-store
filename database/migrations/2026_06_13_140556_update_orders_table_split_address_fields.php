<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('orders', function (Blueprint $table) {

            // новые поля адреса
            $table->string('city')->nullable()->after('id');
            $table->string('street')->nullable()->after('city');
            $table->string('house')->nullable()->after('street');
            $table->string('apartment')->nullable()->after('house');
            $table->string('postal_code')->nullable()->after('apartment');

            // старое поле (если есть)
            if (Schema::hasColumn('orders', 'delivery_address')) {
                $table->dropColumn('delivery_address');
            }
        });
    }

    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {

            $table->string('delivery_address')->nullable();

            $table->dropColumn([
                'city',
                'street',
                'house',
                'apartment',
                'postal_code'
            ]);
        });
    }
};
