<?php

namespace Redberry\LaravelPackageInit\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @see \Redberry\LaravelPackageInit\LaravelPackageInit
 *
 * @method static initialize(mixed $vendor, mixed $name)
 */
class PackageInitiator extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return \Redberry\LaravelPackageInit\PackageInitiator::class;
    }
}
