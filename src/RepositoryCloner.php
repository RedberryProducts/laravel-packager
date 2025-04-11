<?php

namespace Redberry\LaravelPackageInit;

use Illuminate\Filesystem\Filesystem;

readonly class RepositoryCloner
{
    public function __construct(private Filesystem $filesystem, private CommandRunner $commandRunner)
    {
    }

    /**
     * Clone a repository into the specified path.
     *
     * @param  string  $path
     * @param  string  $url
     * @param  string  $branch
     * @param  bool  $unlink
     *
     * @return void
     */
    public function clone(string $path, string $url, string $branch = 'main', bool $unlink = false)
    {
        $this->commandRunner->run("git clone --branch {$branch} {$url} {$path}");

        if ($unlink) {
            $this->filesystem->deleteDirectory($path.'/.git');
        }
    }

}
