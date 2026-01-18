<?php

namespace Database\Seeders\Product;

use App\Models\Product\Section;
use Illuminate\Database\Seeder;
use App\Models\Product\SectionProduct;

class SectionSeeder extends Seeder
{
    public function run()
    {
        $sections = [];

        for ($i = 1; $i <= 2; $i++) {
            $sections[] = [
                "title"  => "Section $i",
                "status" => "active",
            ];
        }

        Section::insert($sections);

        $sectionId = 1;
        for ($i = 1; $i <= 10; $i++) {
            SectionProduct::create([
                "section_id" => $sectionId,
                "product_id" => $i
            ]);

            if ($i == 10) {
                $sectionId = 2;
            }
        }
    }
}
