<?php

use App\Enums\StatusEnum;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->foreignId('payment_gateway_id')->nullable()->constrained('payment_gateways')->noActionOnDelete();
            $table->foreignId('delivery_gateway_id')->nullable()->constrained('delivery_gateways')->noActionOnDelete();
            $table->foreignId('current_status_id')->nullable()->constrained('statuses')->noActionOnDelete();
            $table->foreignId('coupon_id')->nullable()->constrained('coupons')->noActionOnDelete();
            $table->foreignId('order_from_id')->nullable()->constrained('order_froms')->noActionOnDelete();
            $table->foreignId('courier_id')->nullable()->constrained('couriers')->noActionOnDelete();
            $table->foreignId('district_id')->nullable()->constrained('districts')->noActionOnDelete();
            $table->foreignId('customer_type_id')->nullable()->constrained('customer_types')->noActionOnDelete();
            $table->foreignId('cancel_reason_id')->nullable()->constrained('cancel_reasons')->noActionOnDelete();
            $table->string('invoice_number')->nullable();
            $table->decimal('buy_price', 20, 2)->default(0);
            $table->decimal('mrp', 20, 2)->default(0);
            $table->decimal('discount', 20, 2)->default(0);
            $table->decimal('sell_price', 20, 2)->default(0);
            $table->decimal('special_discount', 20, 2)->default(0);
            $table->decimal('coupon_value', 20, 2)->default(0);
            $table->decimal('additional_cost', 8, 2)->default(0)->comment("Like marketing, facebook cost");
            $table->decimal('raw_material_cost', 8, 2)->default(0);
            $table->decimal('net_order_price', 20, 2)->default(0);
            $table->decimal('delivery_charge', 20, 2)->default(0);
            $table->decimal('advance_payment', 20, 2)->default(0);
            $table->decimal('payable_price', 20, 2)->default(0);
            $table->decimal('due', 20, 2)->default(0);
            $table->decimal('courier_payable', 20, 2)->default(0);
            $table->string('paid_status')->default(StatusEnum::UNPAID);
            $table->boolean('is_duplicate')->default(false);
            $table->boolean('is_follow_order')->default(false);
            $table->boolean('is_down_sell')->default(false);
            $table->boolean('is_incomplete')->default(false);
            $table->boolean('is_invoice_printed')->default(false);
            $table->string('phone_number')->nullable();
            $table->string('customer_name')->nullable();
            $table->text('address_details')->nullable()->comment('Shipping address');
            $table->foreignId('block_user_id')->nullable();
            $table->foreignId('locked_by_id')->nullable()->constrained('users');
            $table->timestamp('locked_at')->nullable();
            $table->foreignId('prepared_by')->nullable()->constrained('users');
            $table->timestamp('prepared_at')->nullable();
            $table->foreignId('assign_user_id')->nullable()->constrained('users');
            $table->string('note', 1024)->nullable();
            $table->foreignId('courier_status_id')->nullable()->constrained('statuses')->noActionOnDelete();
            $table->foreignId('pickup_store_id')->nullable()->comment('This id come form pathao or redx');
            $table->integer('delivery_type')->default(48)->comment('48 for normal 12 for on need for pathao');
            $table->foreignId('city_id')->nullable()->comment('This id come form pathao');
            $table->foreignId('zone_id')->nullable()->comment('This id come form pathao');
            $table->foreignId('area_id')->nullable()->comment('This id come form pathao or redx');
            $table->string('delivery_area')->nullable()->comment('This area name come form redx');
            $table->decimal('item_weight', 8, 2)->nullable()->comment('This field needed for courier');
            $table->string('consignment_id')->nullable()->comment('Come from steadfast');
            $table->string('tracking_code')->nullable()->comment('Come from steadfast or pathao');
            $table->json('callback_response')->nullable()->comment("Courier callback response");
            $table->unsignedBigInteger('created_by')->nullable();
            $table->unsignedBigInteger('updated_by')->nullable();
            $table->unsignedBigInteger('deleted_by')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};
