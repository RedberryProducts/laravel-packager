<?php

namespace Redberry\LaravelPackager\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @see \Redberry\LaravelPackager\LaravelPackager
 *
 * @method static void initialize(string $vendor, string $package, array $options = [])
 */
class PackageInitiator extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return \Redberry\LaravelPackager\PackageInitiator::class;
    }
}
