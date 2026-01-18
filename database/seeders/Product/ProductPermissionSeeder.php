<?php

namespace Database\Seeders\Product;

use App\Helpers\Helper;
use Illuminate\Database\Seeder;

class ProductPermissionSeeder extends Seeder
{
    public function run()
    {
        $rolesStructure = [
            'superadmin' => [
                'brands'                => 'c,r,u,d',
                'categories'            => 'c,r,u,d',
                'product-types'         => 'c,r,u,d',
                'attributes'            => 'c,r,u,d',
                'attribute-values'      => 'c,r,u,d',
                'products'              => 'c,r,u,d',
                'category-sections'     => 'c,r,u,d',
                'sections'              => 'c,r,u,d',
                'product-catalogs'      => 'c,r,u,d',
                'warranties'            => 'c,r,u,d',
                'sub-categories'        => 'c,r,u,d',
                'sub-sub-categories'    => 'c,r,u,d',
                'reviews'               => 'c,r,u,d',
                'thank-you-page-offers' => 'c,r,u,d',
                'campaigns'             => 'c,r,u,d',
            ],
            'staff' => [
                'brands'                => 'c,r,u',
                'categories'            => 'c,r,u',
                'product-types'         => 'c,r,u',
                'attributes'            => 'c,r,u',
                'attribute-values'      => 'c,r,u',
                'products'              => 'c,r,u',
                'category-sections'     => 'c,r,u',
                'sections'              => 'c,r,u',
                'product-catalogs'      => 'c,r,u',
                'warranties'            => 'c,r,u',
                'sub-categories'        => 'c,r,u',
                'sub-sub-categories'    => 'c,r,u',
                'reviews'               => 'c,r,u',
                'thank-you-page-offers' => 'c,r,u',
                'campaigns'             => 'c,r,u',
            ],
        ];

        Helper::createRolePermission($rolesStructure, "Product", $this->command);
    }
}
