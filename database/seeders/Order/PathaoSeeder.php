<?php

namespace Database\Seeders\Order;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;

class PathaoSeeder extends Seeder
{
    public function run(): void
    {
        $citiesSql = File::get(database_path('sql/pathao_cities.sql'));
        $areasSql  = File::get(database_path('sql/pathao_areas.sql'));

        DB::unprepared($citiesSql);
        DB::unprepared($areasSql);
    }
}
