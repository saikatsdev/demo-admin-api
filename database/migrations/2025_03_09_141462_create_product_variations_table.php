<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('product_variations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_id')->nullable()->constrained('products')->cascadeOnDelete();
            $table->foreignId('attribute_value_id_1')->nullable()->constrained('attribute_values')->cascadeOnDelete();
            $table->foreignId('attribute_value_id_2')->nullable()->constrained('attribute_values')->cascadeOnDelete();
            $table->foreignId('attribute_value_id_3')->nullable()->constrained('attribute_values')->cascadeOnDelete();
            $table->unsignedInteger('total_purchase_qty')->default(0);
            $table->unsignedInteger('total_sell_qty')->default(0);
            $table->integer('current_stock')->nullable();
            $table->boolean('is_default')->default(false);
            $table->decimal('buy_price', 20, 2)->default(0);
            $table->decimal('mrp', 20, 2)->default(0);
            $table->decimal('offer_price', 20, 2)->default(0);
            $table->decimal('discount', 20, 2)->default(0);
            $table->decimal('sell_price', 20, 2)->default(0);
            $table->decimal('offer_percent', 20, 2)->default(0);
            $table->string('img_path')->nullable();
            $table->text('description')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('product_variations');
    }
};
