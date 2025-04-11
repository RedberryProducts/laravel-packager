<?php

namespace Redberry\LaravelPackageInit;

use Illuminate\Contracts\Filesystem\FileNotFoundException;
use Illuminate\Filesystem\Filesystem;

class ComposerJsonUpdater
{
    public function __construct(private Filesystem $filesystem)
    {
    }

    /**
     * Update the composer.json file with the vendor and name.
     *
     * @param string $packagePath
     * @param string $vendor
     * @param string $name
     * @throws FileNotFoundException
     */
    public function addRepository(string $packagePath, string $vendor, string $name): void
    {
        $composerPath = base_path('composer.json');
        $composerData = $this->getComposer($composerPath);

        $updated = false;

        if (!$this->repoExists($composerData['repositories'] ?? [], $packagePath)) {
            $composerData = $this->addPackageRepository($composerData, $packagePath);
            $updated = true;
        }

        if ($this->needsRequirement($composerData, $vendor, $name)) {
            $composerData = $this->addRequirement($composerData, $vendor, $name);
            $updated = true;
        }

        if ($updated) {
            $this->saveComposer($composerData, $composerPath);
        }
    }

    /**
     * Check if the package needs to be added to require-dev.
     *
     * @param array $composerData
     * @param string $vendor
     * @param string $name
     * @return bool
     */
    private function needsRequirement(array $composerData, string $vendor, string $name): bool
    {
        $packageName = "{$vendor}/{$name}";
        return !isset($composerData['require-dev'][$packageName]);
    }

    /**
     * Add the package to the require-dev section.
     *
     * @param array $composerData
     * @param string $vendor
     * @param string $name
     * @return array
     */
    private function addRequirement(array $composerData, string $vendor, string $name): array
    {
        if (!isset($composerData['require-dev'])) {
            $composerData['require-dev'] = [];
        }

        $packageName = "{$vendor}/{$name}";
        $composerData['require-dev'][$packageName] = '*';

        return $composerData;
    }

    /**
     * Add the package repository to composer.json.
     *
     * @param array $composerData
     * @param string $packagePath
     * @return array
     */
    private function addPackageRepository(array $composerData, string $packagePath): array
    {
        if (!isset($composerData['repositories'])) {
            $composerData['repositories'] = [];
        }

        $composerData['repositories'][] = [
            'type' => 'path',
            'url' => './' . $packagePath,
            'options' => ['symlink' => true],
        ];

        return $composerData;
    }

    /**
     * Save the updated composer.json data.
     *
     * @param array $composerData
     * @param string $composerPath
     * @throws \Exception
     */
    private function saveComposer(array $composerData, string $composerPath): void
    {
        $updatedJson = json_encode($composerData, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);

        if ($updatedJson === false) {
            throw new \Exception('Failed to encode updated composer.json');
        }

        $this->filesystem->put($composerPath, $updatedJson);
    }

    /**
     * Check if the repository already exists in composer.json.
     *
     * @param array $repositories
     * @param string $packagePath
     * @return bool
     */
    private function repoExists(array $repositories, string $packagePath): bool
    {
        foreach ($repositories as $repo) {
            if (isset($repo['type']) && $repo['type'] === 'path' && $repo['url'] === '.' . $packagePath) {
                return true;
            }
        }
        return false;
    }

    /**
     * Get and validate the composer.json content.
     *
     * @param string $composerPath
     * @return array
     * @throws \Exception
     */
    private function getComposer(string $composerPath): array
    {
        if (!$this->filesystem->exists($composerPath)) {
            throw new \Exception("Application composer.json not found at {$composerPath}");
        }

        $composerJson = $this->filesystem->get($composerPath);
        $composerData = json_decode($composerJson, true);

        if (json_last_error() !== JSON_ERROR_NONE) {
            throw new \Exception('Invalid composer.json: ' . json_last_error_msg());
        }

        return $composerData;
    }
}
