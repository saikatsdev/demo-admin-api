<?php

use App\Enums\DiscountTypeEnum;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('up_sell_details', function (Blueprint $table) {
            $table->id();
            $table->foreignId('up_sell_id')->constrained('up_sells')->cascadeOnDelete();
            $table->foreignId('trigger_product_id')->constrained('products')->cascadeOnDelete();
            $table->foreignId('up_sell_product_id')->constrained('products')->cascadeOnDelete();
            $table->string('custom_name')->nullable();
            $table->string('discount_type')->default(DiscountTypeEnum::FIXED);
            $table->decimal('discount_amount', 10, 2)->default(0);
            $table->decimal('calculated_amount', 10, 2)->default(0);
            $table->timestamps();

            $table->unique(['up_sell_id', 'trigger_product_id', 'up_sell_product_id'], 'uniq_up_sell_trigger_offer');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('up_sell_details');
    }
};
