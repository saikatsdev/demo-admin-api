<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('up_sell_settings', function (Blueprint $table) {
            $table->id();
            $table->string('greetings');
            $table->string('title');
            $table->string('sub_title');
            $table->string('button_text');
            $table->string('button_text_color');
            $table->string('button_bg_color');
            $table->unsignedBigInteger('created_by')->nullable();
            $table->unsignedBigInteger('updated_by')->nullable();
            $table->unsignedBigInteger('deleted_by')->nullable();
            $table->softDeletes();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('up_sell_settings');
    }
};
