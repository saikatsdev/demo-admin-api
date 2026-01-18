<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug');
            $table->foreignId('brand_id')->nullable()->constrained('brands')->nullOnDelete();
            $table->foreignId('category_id')->nullable()->constrained('categories')->nullOnDelete();
            $table->foreignId('sub_category_id')->nullable()->constrained('sub_categories')->nullOnDelete();
            $table->foreignId('sub_sub_category_id')->nullable()->constrained('sub_sub_categories')->nullOnDelete();
            $table->foreignId('product_type_id')->nullable()->constrained('product_types')->nullOnDelete();
            $table->decimal('buy_price', 20, 2)->default(0);
            $table->decimal('mrp', 20, 2)->default(0);
            $table->decimal('offer_price', 20, 2)->default(0);
            $table->decimal('discount', 20, 2)->default(0);
            $table->decimal('sell_price', 20, 2)->default(0);
            $table->decimal('offer_percent', 20, 2)->default(0);
            $table->integer('total_purchase_qty')->default(0);
            $table->integer('total_sell_qty')->default(0);
            $table->integer('current_stock')->default(0);
            $table->integer('minimum_qty')->default(1)->comment("Minimum order quantity");
            $table->integer('alert_qty')->nullable();
            $table->string('status')->default('active');
            $table->string('sku')->nullable();
            $table->integer("incomplete_order_count")->default(0);
            $table->boolean('free_shipping')->default(false);
            $table->string('img_path')->nullable();
            $table->text('description')->nullable();
            $table->text('short_description')->nullable();
            $table->text('video_url')->nullable();
            $table->string('meta_title')->nullable();
            $table->text('meta_keywords')->nullable();
            $table->text('meta_description')->nullable();
            $table->unsignedBigInteger('created_by')->nullable();
            $table->unsignedBigInteger('updated_by')->nullable();
            $table->unsignedBigInteger('deleted_by')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};
