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
    ],
    'packages_directory' => 'packages',
];
