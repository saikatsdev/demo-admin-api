<?php

namespace Database\Seeders;

use App\Helpers\Helper;
use Illuminate\Database\Seeder;

class CMSPermissionSeeder extends Seeder
{
    public function run()
    {
        $rolesStructure = [
            'superadmin' => [
                'sliders'              => 'c,r,u,d',
                'banners'              => 'c,r,u,d',
                'abouts'               => 'c,r,u,d',
                'contacts'             => 'c,r,u,d',
                'faqs'                 => 'c,r,u,d',
                'social-medias'        => 'c,r,u,d',
                'privacy-policies'     => 'c,r,u,d',
                'terms-and-conditions' => 'c,r,u,d',
            ],
            'staff' => [
                'sliders'              => 'c,r,u',
                'banners'              => 'c,r,u',
                'abouts'               => 'c,r,u',
                'contacts'             => 'c,r,u',
                'faqs'                 => 'c,r,u',
                'social-medias'        => 'c,r,u',
                'privacy-policies'     => 'c,r,u',
                'terms-and-conditions' => 'c,r,u',
            ],
        ];

        Helper::createRolePermission($rolesStructure, "CMS", $this->command);
    }
}
