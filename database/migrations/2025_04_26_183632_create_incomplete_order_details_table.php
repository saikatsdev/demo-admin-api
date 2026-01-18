<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    public function up()
    {
        Schema::create('incomplete_order_details', function (Blueprint $table) {
            $table->id();
            $table->foreignId('incomplete_order_id')->constrained('incomplete_orders')->cascadeOnUpdate();
            $table->foreignId('product_id')->constrained('products')->cascadeOnDelete();
            $table->foreignId('attribute_value_id_1')->nullable()->constrained('attribute_values')->cascadeOnDelete();
            $table->foreignId('attribute_value_id_2')->nullable()->constrained('attribute_values')->cascadeOnDelete();
            $table->foreignId('attribute_value_id_3')->nullable()->constrained('attribute_values')->cascadeOnDelete();
            $table->string('note')->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('incomplete_order_details');
    }
};
