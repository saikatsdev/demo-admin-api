<?php

namespace Database\Seeders;

use App\Enums\StatusEnum;
use App\Models\BlogPost\Tag;
use Illuminate\Database\Seeder;

class TagSeeder extends Seeder
{
    public function run()
    {
        for ($i = 0; $i < 5; $i++) {
            Tag::create([
                'name'   => "Tag $i",
                'slug'   => "tag-$i",
                'status' => StatusEnum::ACTIVE,
            ]);
        }
    }
}
