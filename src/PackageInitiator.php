<?php

namespace Redberry\LaravelPackager;

use Illuminate\Contracts\Filesystem\FileNotFoundException;
use Illuminate\Filesystem\Filesystem;

class PackageInitiator
{
    private array $config;

    public function __construct(
        private RepositoryCloner $cloner,
        private Filesystem $filesystem,
        private CommandRunner $commandRunner,
        private ComposerJsonUpdater $composerJsonUpdater,
    ) {
        $this->config = config('packager');
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
        $branch = $this->config['skeletons'][$this->config['default_skeleton']]['branch'];

        $this->cloneRepository($packagePath, $packageUrl, $branch);

        $this->updateComposerJson($packagePath, $vendor, $name);

        $this->runConfigurationCommands($packagePath);
    }

    /**
     * @throws FileNotFoundException
     */
    private function updateComposerJson(string $packagePath, string $vendor, string $name): void
    {
        $this->composerJsonUpdater->addRepository($packagePath, $vendor, $name);
    }

    /**
     * @throws \Exception
     */
    public function cloneRepository(string $packagePath, mixed $packageUrl, string $branch): void
    {
        if ($this->filesystem->exists($packagePath)) {
            throw new \Exception("Package already exists at {$packagePath}");
        }

        $this->cloner->clone($packagePath, $packageUrl, $branch, true);
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
