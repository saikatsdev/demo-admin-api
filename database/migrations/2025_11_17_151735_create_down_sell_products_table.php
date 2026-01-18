<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('down_sell_product', function (Blueprint $table) {
            $table->foreignId('down_sell_id')->constrained('down_sells')->cascadeOnDelete();
            $table->foreignId('product_id')->constrained('products')->cascadeOnDelete();

            $table->primary(['down_sell_id', 'product_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('down_sell_product');
    }
};
