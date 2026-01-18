<?php

namespace Database\Seeders;

use App\Models\User;
use App\Enums\StatusEnum;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class AdminSeeder extends Seeder
{
    public function run(): void
    {
       User::insert([
            [
                "user_category_id" => 2,
                "username"         => "admin",
                "phone_number"     => "01764997485",
                "email"            => "superadmin@gmail.com",
                "is_verified"      => 1,
                "status"           => StatusEnum::ACTIVE,
                "password"         => Hash::make("123456789"),
            ],
            [
                "user_category_id" => 2,
                "username"         => "admin",
                "phone_number"     => "01686381998",
                "email"            => "admin@gmail.com",
                "is_verified"      => 1,
                "status"           => StatusEnum::ACTIVE,
                "password"         => Hash::make("123456789"),
            ],
            [
                "user_category_id" => 2,
                "username"         => "servicekey",
                "phone_number"     => "01700000017",
                "email"            => "servicekey@gmail.com",
                "is_verified"      => 1,
                "status"           => StatusEnum::ACTIVE,
                "password"         => Hash::make("123456789"),
            ],
            [
                "user_category_id" => 2,
                "username"         => "Staff",
                "phone_number"     => "01000000000",
                "email"            => "staff@gmail.com",
                "is_verified"      => 1,
                "status"           => StatusEnum::ACTIVE,
                "password"         => Hash::make("123456789"),
            ]
        ]);

        DB::table("role_user")->insert([
            [
                "role_id"   => 1,
                "user_id"   => 1,
                "user_type" => "App\Models\User"
            ],
            [
                "role_id"   => 1,
                "user_id"   => 2,
                "user_type" => "App\Models\User"
            ],
            [
                "role_id"   => 1,
                "user_id"   => 3,
                "user_type" => "App\Models\User"
            ],
            [
                "role_id"   => 3,
                "user_id"   => 4,
                "user_type" => "App\Models\User"
            ]
        ]);
    }
}
