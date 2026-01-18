<?php

use App\Enums\StatusEnum;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('product_reviews', function (Blueprint $table) {
            $table->id();
            $table->foreignId("product_id")->constrained('products')->cascadeOnDelete();
            $table->string("name");
            $table->string("email")->nullable();
            $table->string("image")->nullable();
            $table->string("title")->nullable();
            $table->unsignedInteger("rating");
            $table->string("review")->nullable();
            $table->string("status")->default(StatusEnum::PENDING);
            $table->unsignedBigInteger('created_by')->nullable();
            $table->unsignedBigInteger('updated_by')->nullable();
            $table->unsignedBigInteger('deleted_by')->nullable();
            $table->softDeletes();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('product_reviews');
    }
};
