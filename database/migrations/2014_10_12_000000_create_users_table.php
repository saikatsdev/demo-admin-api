<?php

use App\Enums\StatusEnum;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('username');
            $table->string('phone_number');
            $table->string('email')->nullable();
            $table->string('verification_otp')->nullable();
            $table->string('status')->default(StatusEnum::INACTIVE);
            $table->boolean('is_verified')->default(0);
            $table->unsignedBigInteger('bonus_points')->default(0);
            $table->foreignId('user_category_id')->nullable();
            $table->foreignId('manager_id')->nullable();
            $table->decimal('salary', 10, 2)->default(0);
            $table->string('staff_login_otp')->nullable();
            $table->timestamp('login_at')->nullable();
            $table->timestamp('logout_at')->nullable();
            $table->string('home_address')->nullable();
            $table->string('office_address')->nullable();
            $table->timestamp('email_verified_at')->nullable();
            $table->string('img_path')->nullable();
            $table->string('password');
            $table->date('dob')->nullable()->comment("date_of_birth");
            $table->unsignedBigInteger('deleted_by')->nullable();
            $table->rememberToken();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('users');
    }
};
