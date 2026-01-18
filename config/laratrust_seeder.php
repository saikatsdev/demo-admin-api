<?php

return [
    'create_users' => false,

    'truncate_tables' => true,

    'roles_structure' => [
        'superadmin' => [
            'dashboards'       => 'r',
            'roles'            => 'c,r,u,d',
            'permissions'      => 'c,r,u,d',
            'settings'         => 'c,r,u,d',
            'setting-category' => 'c,r,u,d',
            'user-categories'  => 'c,r,u,d',
            'users'            => 'c,r,u,d',
            'reports'          => 'r',
        ],
        'teamlead' => [
            'dashboards'       => 'r',
            'roles'            => 'c,r,u,d',
            'permissions'      => 'c,r,u,d',
            'settings'         => 'c,r,u,d',
            'setting-category' => 'c,r,u,d',
            'user-categories'  => 'c,r,u,d',
            'users'            => 'c,r,u,d',
            'reports'          => 'r',
        ],
        'staff' => [
            'dashboards'  => 'r',
            'users'       => 'r',
            'permissions' => 'r',
            'users'       => 'r',
            'settings'    => 'r',
        ],
    ],

    'permissions_map' => [
        'c' => 'create',
        'r' => 'read',
        'u' => 'update',
        'd' => 'delete',
    ],
];
