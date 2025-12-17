<?php

return [
    [
        'label' => 'General',
        'items' => [
            [
                'label' => 'Panel principal',
                'href' => '/dashboard',
                'icon' => 'pi pi-chart-bar',
                'permission' => 'dashboard.view',
            ],
        ],
    ],
    [
        'label' => 'Administración',
        'items' => [
            [
                'label' => 'Usuarios',
                'href' => '/admin/users',
                'icon' => 'pi pi-users',
                'permission' => 'users.view',
            ],
            [
                'label' => 'Roles',
                'href' => '/admin/roles',
                'icon' => 'pi pi-id-card',
                'permission' => 'roles.view',
            ],
            [
                'label' => 'Permisos',
                'href' => '/admin/permissions',
                'icon' => 'pi pi-lock',
                'permission' => 'permissions.view',
            ],
        ],
    ],
    [
        'label' => 'Configuración',
        'items' => [
            [
                'label' => 'Perfil',
                'href' => '/settings/profile',
                'icon' => 'pi pi-user',
                'permission' => null,
            ],
            [
                'label' => 'Apariencia',
                'href' => '/settings/appearance',
                'icon' => 'pi pi-palette',
                'permission' => null,
            ],
        ],
    ],
];
