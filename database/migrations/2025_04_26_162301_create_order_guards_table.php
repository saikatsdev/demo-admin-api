<?php

use App\Enums\StatusEnum;
use App\Enums\DurationTypeEnum;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('order_guards', function (Blueprint $table) {
            $table->id();
            $table->integer('quantity')->nullable();
            $table->integer('duration')->nullable();
            $table->decimal('allow_percentage', 5, 2)->default(0);
            $table->string('block_message')->default("You are not capable for orders");
            $table->string('permanent_block_message')->default("You are currently blocked for new create new order.");
            $table->string('courier_block_message')->default("You are not capable for orders");
            $table->string('duration_type', 20)->default(DurationTypeEnum::DAYS);
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
        Schema::dropIfExists('order_guards');
    }
};
