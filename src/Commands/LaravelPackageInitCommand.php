<?php

namespace Redberry\LaravelPackageInit\Commands;

use Illuminate\Console\Command;

class LaravelPackageInitCommand extends Command
{
    public $signature = 'laravel-package-init {name}';

    public $description = 'My command';

    public function handle(): int
    {
        $name = $this->argument('name');

        $this->info('Hello '.$name);
        $this->comment('All done');

        return self::SUCCESS;
    }
}
