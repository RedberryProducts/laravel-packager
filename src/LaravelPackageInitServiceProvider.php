<?php

namespace Redberry\LaravelPackageInit;

use Redberry\LaravelPackageInit\Commands\LaravelPackageInitCommand;
use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;

class LaravelPackageInitServiceProvider extends PackageServiceProvider
{
    public function configurePackage(Package $package): void
    {
        /*
         * This class is a Package Service Provider
         *
         * More info: https://github.com/spatie/laravel-package-tools
         */
        $package
            ->name('laravel-package-init')
            ->hasConfigFile()
            ->hasViews()
            ->hasMigration('create_laravel_package_init_table')
            ->hasCommand(LaravelPackageInitCommand::class);
    }
}
