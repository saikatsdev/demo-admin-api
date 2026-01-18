<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('s_e_o_s', function (Blueprint $table) {
            $table->id();
            $table->string('img_path')->nullable();
            $table->string('page');
            $table->string('meta_title');
            $table->string('meta_description')->nullable();
            $table->json('meta_keywords')->nullable();
            $table->integer('width')->default(1260)->nullable();
            $table->integer('height')->default(960)->nullable();
            $table->string('status')->default("published");
            $table->unsignedBigInteger('created_by')->nullable();
            $table->unsignedBigInteger('updated_by')->nullable();
            $table->unsignedBigInteger('deleted_by')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('s_e_o_s');
    }
};
