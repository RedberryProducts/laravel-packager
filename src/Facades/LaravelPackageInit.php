<?php

namespace Redberry\LaravelPackageInit\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @see \Redberry\LaravelPackageInit\LaravelPackageInit
 */
class LaravelPackageInit extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return \Redberry\LaravelPackageInit\LaravelPackageInit::class;
    }
}
