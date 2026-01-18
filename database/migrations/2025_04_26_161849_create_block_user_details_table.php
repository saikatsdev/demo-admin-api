<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    public function up()
    {
        Schema::create('block_user_details', function (Blueprint $table) {
            $table->id();
            $table->foreignId('block_user_id')->constrained('block_users')->cascadeOnDelete();
            $table->string('phone_number');
            $table->string('ip_address')->nullable();
            $table->string('browser')->nullable();
            $table->string('platform')->nullable();
            $table->string('device_type')->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('block_user_details');
    }
};
