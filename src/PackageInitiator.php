<?php

namespace Redberry\LaravelPackageInit;

use Illuminate\Filesystem\Filesystem;

class PackageInitiator
{
    private array $config;

    public function __construct(
        private RepositoryCloner $cloner,
        private Filesystem $filesystem,
        private CommandRunner $commandRunner
    ) {
        $this->config = config('package-init');
    }

    /**
     * Initialize new package into the configured directory.
     *
     *
     * @throws \Exception
     */
    public function initialize(
        $vendor,
        $name
    ): void {
        $packagePath = $this->config['packages_directory'].'/'.$vendor.'/'.$name;
        $packageUrl = $this->config['skeletons'][$this->config['default_skeleton']]['url'];

        $this->cloneRepository($packagePath, $packageUrl);

        $this->updateComposerJson($packagePath, $vendor);

        $this->runConfigurationCommands($packagePath);
    }

    private function updateComposerJson(string $packagePath, $vendor): void
    {
        // TODO: implement this method
    }

    /**
     * @throws \Exception
     */
    public function cloneRepository(string $packagePath, mixed $packageUrl): void
    {
        if ($this->filesystem->exists($packagePath)) {
            throw new \Exception("Package already exists at {$packagePath}");
        }

        $this->cloner->clone($packagePath, $packageUrl, 'main', true);
    }

    private function runConfigurationCommands($packagePath)
    {
        $activeSkeleton = $this->config['skeletons'][$this->config['default_skeleton']];

        $commands = $activeSkeleton['runs'] ?? [];

        foreach ($commands as $command) {
            $this->commandRunner->runInteractive("cd {$packagePath} && ".$command);
        }
    }
}
