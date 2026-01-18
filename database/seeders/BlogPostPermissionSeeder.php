<?php

namespace Database\Seeders;

use App\Helpers\Helper;
use Illuminate\Database\Seeder;

class BlogPostPermissionSeeder extends Seeder
{
    public function run()
    {
        $rolesStructure = [
            'superadmin' => [
                'tags'                 => 'c,r,u,d',
                'blog-posts'           => 'c,r,u,d',
                'blog-post-categories' => 'c,r,u,d',
            ],
            'staff' => [
                'tags'                 => 'c,r,u',
                'blog-posts'           => 'c,r,u',
                'blog-post-categories' => 'c,r,u',
            ],
        ];

        Helper::createRolePermission($rolesStructure, "BlogPost", $this->command);
    }
}
