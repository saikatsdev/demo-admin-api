<?php

use App\Enums\StatusEnum;
use App\Enums\DiscountTypeEnum;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('online_payment_discounts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('payment_gateway_id')->constrained('payment_gateways')->cascadeOnUpdate()->cascadeOnDelete();
            $table->string('discount_type')->default(DiscountTypeEnum::FIXED);
            $table->decimal('discount_amount', 8, 2)->default(0);
            $table->decimal('minimum_cart_amount', 8, 2)->default(0);
            $table->decimal('maximum_discount_amount', 8, 2)->default(0);
            $table->string('status')->default(StatusEnum::ACTIVE);
            $table->unsignedBigInteger('created_by')->nullable();
            $table->unsignedBigInteger('updated_by')->nullable();
            $table->unsignedBigInteger('deleted_by')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('online_payment_discounts');
    }
};
