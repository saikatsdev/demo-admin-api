<?php

use App\Enums\StatusEnum;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('up_sells', function (Blueprint $table) {
            $table->id();
            $table->string("title")->nullable();
            $table->timestamp("started_at")->nullable();
            $table->timestamp("ended_at")->nullable();
            $table->string('status')->default(StatusEnum::ACTIVE);
            $table->boolean('is_all')->default();
            $table->json('trigger_category_ids')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('up_sells');
    }
};
