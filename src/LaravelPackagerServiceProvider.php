<?php

namespace Redberry\LaravelPackager;

use Redberry\LaravelPackager\Commands\MakePackageCommand;
use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;

class LaravelPackagerServiceProvider extends PackageServiceProvider
{
    public function configurePackage(Package $package): void
    {
        /*
         * This class is a Package Service Provider
         *
         * More info: https://github.com/spatie/laravel-package-tools
         */
        $package
            ->name('laravel-packager')
            ->hasConfigFile()
            ->hasCommand(MakePackageCommand::class);
    }
}
