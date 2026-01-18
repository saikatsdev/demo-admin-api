<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    public function up()
    {
        Schema::create('return_or_damage_details', function (Blueprint $table) {
            $table->id();
            $table->foreignId('return_or_damage_id')->constrained('return_or_damages')->cascadeOnDelete();
            $table->foreignId('product_id')->constrained('products')->cascadeOnDelete();
            $table->foreignId('attribute_value_id_1')->nullable()->constrained('attribute_values')->cascadeOnDelete();
            $table->foreignId('attribute_value_id_2')->nullable()->constrained('attribute_values')->cascadeOnDelete();
            $table->foreignId('attribute_value_id_3')->nullable()->constrained('attribute_values')->cascadeOnDelete();
            $table->unsignedInteger('quantity')->default(1);
            $table->decimal('buy_price', 20, 2)->default(0);
            $table->decimal('mrp', 20, 2)->default(0);
            $table->decimal('discount', 20, 2)->default(0);
            $table->decimal('sell_price', 20, 2)->default(0);
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('return_or_damage_details');
    }
};
