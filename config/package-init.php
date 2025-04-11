<?php

return [
    'default_skeleton' => 'spatie',
    'skeletons' => [
        'spatie' => [
            'url' => 'git@github.com:spatie/package-skeleton-laravel.git',
            'branch' => 'main',
            'runs' => [
                'php configure.php',
            ],
        ],
        'redberry' => [
            'url' => 'git@github.com:spatie/package-skeleton-laravel.git',
            'branch' => 'main',
            'runs' => [
                'php configure.php',
            ],
        ],
    ],
    'packages_directory' => base_path('packages'),
];
