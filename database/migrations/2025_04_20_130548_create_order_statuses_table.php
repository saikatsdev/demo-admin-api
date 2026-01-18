<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    public function up()
    {
        Schema::create('order_statuses', function (Blueprint $table) {
            $table->foreignId('order_id')->constrained('orders')->onUpdate('cascade');
            $table->foreignId('status_id')->constrained('statuses')->onUpdate('cascade');
            $table->timestamps();

            $table->primary(['order_id', 'status_id']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('order_statuses');
    }
};
