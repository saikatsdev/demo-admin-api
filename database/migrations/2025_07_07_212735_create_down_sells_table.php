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
        Schema::create('down_sells', function (Blueprint $table) {
            $table->id();
            $table->string('title')->nullable();
            $table->string('type')->default(DiscountTypeEnum::FIXED);
            $table->decimal('amount', 20, 2)->default(0);
            $table->integer('duration')->nullable()->comment("In second");
            $table->timestamp('started_at')->nullable();
            $table->timestamp('ended_at')->nullable();
            $table->string('description')->nullable();
            $table->string('status')->default(StatusEnum::ACTIVE);
            $table->string('img_path')->nullable();
            $table->unsignedBigInteger('created_by')->nullable();
            $table->unsignedBigInteger('updated_by')->nullable();
            $table->unsignedBigInteger('deleted_by')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('down_sells');
    }
};
