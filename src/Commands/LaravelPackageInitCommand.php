<?php

namespace Redberry\LaravelPackageInit\Commands;

use Illuminate\Console\Command;
use Redberry\LaravelPackageInit\Facades\PackageInitiator;

class LaravelPackageInitCommand extends Command
{
    public $signature = 'make:package {name?}';

    public $description = 'Create a new Laravel package in the specified directory.';

    public function handle(): int
    {
        [$vendor, $name] = $this->getVendorAndPackageName();

        try {
            PackageInitiator::initialize($vendor, $name);
            $this->info("Package {$vendor}/{$name} created successfully!");
        } catch (\Exception $e) {
            $this->error('Failed to create package: '.$e->getMessage());

            return self::FAILURE;
        }

        return self::SUCCESS;
    }

    public function getVendorAndPackageName(): array
    {
        $name = $this->argument('name') ?? $this->ask('Enter package name');

        if (str_contains($name, '/')) {
            [$vendor, $name] = explode('/', $name);
        } else {
            $vendor = $this->ask('Enter vendor name (e.g., redberry)', 'redberry');
        }

        return [$vendor, $name];
    }
}
