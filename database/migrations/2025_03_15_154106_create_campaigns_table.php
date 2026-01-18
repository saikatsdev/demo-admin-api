<?php

use App\Enums\StatusEnum;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('campaigns', function (Blueprint $table) {
            $table->id();
            $table->string("title");
            $table->string("slug");
            $table->timestamp("start_date");
            $table->timestamp("end_date")->nullable();
            $table->string('status')->default(StatusEnum::INACTIVE);
            $table->unsignedBigInteger("created_by")->nullable();
            $table->unsignedBigInteger("updated_by")->nullable();
            $table->unsignedBigInteger("deleted_by")->nullable();
            $table->string('img_path')->nullable();
            $table->unsignedBigInteger('width')->default(4360);
            $table->unsignedBigInteger('height')->default(1826);
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('campaigns');
    }
};
