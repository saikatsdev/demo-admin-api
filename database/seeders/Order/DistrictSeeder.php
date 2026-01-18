<?php

namespace Database\Seeders\Order;

use App\Enums\StatusEnum;
use Illuminate\Support\Str;
use App\Models\Order\District;
use Illuminate\Database\Seeder;

class DistrictSeeder extends Seeder
{
    public function run()
    {
        $districts = [
            // Dhaka Division
            "Dhaka",
            "Faridpur",
            "Gazipur",
            "Gopalganj",
            "Kishoreganj",
            "Madaripur",
            "Manikganj",
            "Munshiganj",
            "Narayanganj",
            "Narsingdi",
            "Rajbari",
            "Shariatpur",
            "Tangail",

            // Chattogram Division
            "Bandarban",
            "Brahmanbaria",
            "Chandpur",
            "Chattogram",
            "Cox\"s Bazar",
            "Cumilla",
            "Feni",
            "Khagrachari",
            "Lakshmipur",
            "Noakhali",
            "Rangamati",

            // Rajshahi Division
            "Bogura",
            "Joypurhat",
            "Naogaon",
            "Natore",
            "Chapainawabganj",
            "Pabna",
            "Rajshahi",
            "Sirajganj",

            // Khulna Division
            "Bagerhat",
            "Chuadanga",
            "Jashore",
            "Jhenaidah",
            "Khulna",
            "Kushtia",
            "Magura",
            "Meherpur",
            "Narail",
            "Satkhira",

            // Barishal Division
            "Barishal",
            "Barguna",
            "Bhola",
            "Jhalokati",
            "Patuakhali",
            "Pirojpur",

            // Sylhet Division
            "Habiganj",
            "Moulvibazar",
            "Sunamganj",
            "Sylhet",

            // Rangpur Division
            "Dinajpur",
            "Gaibandha",
            "Kurigram",
            "Lalmonirhat",
            "Nilphamari",
            "Panchagarh",
            "Rangpur",
            "Thakurgaon",

            // Mymensingh Division
            "Jamalpur",
            "Mymensingh",
            "Netrokona",
            "Sherpur"
        ];

        $data   = [];
        $status = StatusEnum::ACTIVE;
        $now    = now();

        foreach ($districts as $district) {
            $data[] = [
                "name"       => $district,
                "slug"       => Str::slug($district),
                "status"     => $status,
                "created_at" => $now,
                "updated_at" => $now
            ];
        }

        District::insert($data);
    }
}
