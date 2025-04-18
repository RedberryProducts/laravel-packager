<?php

use Illuminate\Filesystem\Filesystem;
use Mockery as Mock;
use Redberry\LaravelPackager\PackageInitiator;

beforeEach(function () {
    // Set up the configuration
    config([
        'packager' => [
            'packages_directory' => '/packages',
            'default_skeleton' => 'laravel',
            'skeletons' => [
                'laravel' => [
                    'url' => 'https://github.com/laravel/skeleton.git',
                    'branch' => 'main',
                    'runs' => ['composer install', 'npm install'],
                ],
            ],
        ],
    ]);

    // Mock dependencies
    $this->cloner = Mock::mock(\Redberry\LaravelPackager\RepositoryCloner::class);
    $this->filesystem = Mock::mock(Filesystem::class);
    $this->commandRunner = Mock::mock(\Redberry\LaravelPackager\CommandRunner::class);
    $this->composerUpdater = Mock::mock(\Redberry\LaravelPackager\ComposerJsonUpdater::class);

    // Instantiate the class
    $this->initiator = new PackageInitiator(
        $this->cloner,
        $this->filesystem,
        $this->commandRunner,
        $this->composerUpdater,
    );
});

afterEach(function () {
    Mock::close();
});

it('initializes a new package successfully', function () {
    $vendor = 'redberry';
    $name = 'example';
    $packagePath = "/packages/{$vendor}/{$name}";
    $packageUrl = 'https://github.com/laravel/skeleton.git';

    // Mock filesystem to say package doesn't exist
    $this->filesystem->shouldReceive('exists')
        ->with($packagePath)
        ->once()
        ->andReturn(false);

    // Mock cloner
    $this->cloner->shouldReceive('clone')
        ->with($packagePath, $packageUrl, 'main', true)
        ->once();

    // Mock command runner
    $this->commandRunner->shouldReceive('runInteractive')
        ->with("cd {$packagePath} && composer install")
        ->once();

    $this->composerUpdater->shouldReceive('addRepository');

    $this->commandRunner->shouldReceive('runInteractive')
        ->with("cd {$packagePath} && npm install")
        ->once();

    // Run initialize
    $this->initiator->initialize($vendor, $name);

    // Since updateComposerJson is empty, we expect it to do nothing
    expect(true)->toBeTrue(); // Placeholder for composer.json assertions
});

it('throws an exception if package directory already exists', function () {
    $vendor = 'redberry';
    $name = 'example';
    $packagePath = "/packages/{$vendor}/{$name}";

    // Mock filesystem to say package exists
    $this->filesystem->shouldReceive('exists')
        ->with($packagePath)
        ->once()
        ->andReturn(true);

    $this->composerUpdater->shouldNotReceive('addRepository');

    // Expect no cloning or commands
    $this->cloner->shouldNotReceive('clone');
    $this->commandRunner->shouldNotReceive('runInteractive');

    // Expect exception
    expect(fn () => $this->initiator->initialize($vendor, $name))
        ->toThrow(\Exception::class, "Package already exists at {$packagePath}");
});

it('handles empty configuration commands', function () {
    // Override config with no runs
    config(['packager.skeletons.laravel.runs' => []]);
    // Instantiate the class
    $this->initiator = new PackageInitiator(
        $this->cloner,
        $this->filesystem,
        $this->commandRunner,
        $this->composerUpdater
    );

    $vendor = 'redberry';
    $name = 'example';
    $packagePath = "/packages/{$vendor}/{$name}";
    $packageUrl = 'https://github.com/laravel/skeleton.git';

    // Mock filesystem
    $this->filesystem->shouldReceive('exists')
        ->with($packagePath)
        ->once()
        ->andReturn(false);

    $this->composerUpdater->shouldReceive('addRepository');

    // Mock cloner
    $this->cloner->shouldReceive('clone')
        ->with($packagePath, $packageUrl, 'main', true)
        ->once();

    // Expect no commands
    $this->commandRunner->shouldNotReceive('runInteractive');

    // Run initialize
    $this->initiator->initialize($vendor, $name);
});

it('handles missing runs key in skeleton config', function () {
    // Override config with no runs key
    config([
        'packager.skeletons.laravel' => [
            'url' => 'https://github.com/laravel/skeleton.git',
            'branch' => 'main',
        ],
    ]);

    // Instantiate the class
    $this->initiator = new PackageInitiator(
        $this->cloner,
        $this->filesystem,
        $this->commandRunner,
        $this->composerUpdater
    );

    $vendor = 'redberry';
    $name = 'example';
    $packagePath = "/packages/{$vendor}/{$name}";
    $packageUrl = 'https://github.com/laravel/skeleton.git';

    // Mock filesystem
    $this->filesystem->shouldReceive('exists')
        ->with($packagePath)
        ->once()
        ->andReturn(false);

    // Mock cloner
    $this->cloner->shouldReceive('clone')
        ->with($packagePath, $packageUrl, 'main', true)
        ->once();

    // Expect no commands
    $this->commandRunner->shouldNotReceive('runInteractive');

    $this->composerUpdater->shouldReceive('addRepository');

    // Run initialize
    $this->initiator->initialize($vendor, $name);
});

it('handles different branch names in config', function () {
    // Override config with no runs key
    config([
        'packager.skeletons.laravel' => [
            'url' => 'https://github.com/laravel/skeleton.git',
            'branch' => 'test-branch',
        ],
    ]);

    // Instantiate the class
    $this->initiator = new PackageInitiator(
        $this->cloner,
        $this->filesystem,
        $this->commandRunner,
        $this->composerUpdater
    );

    $vendor = 'redberry';
    $name = 'example';
    $packagePath = "/packages/{$vendor}/{$name}";
    $packageUrl = 'https://github.com/laravel/skeleton.git';

    // Mock filesystem
    $this->filesystem->shouldReceive('exists')
        ->with($packagePath)
        ->once()
        ->andReturn(false);

    // Mock cloner
    $this->cloner->shouldReceive('clone')
        ->with($packagePath, $packageUrl, 'test-branch', true)
        ->once();

    // Expect no commands
    $this->commandRunner->shouldNotReceive('runInteractive');

    $this->composerUpdater->shouldReceive('addRepository');

    // Run initialize
    $this->initiator->initialize($vendor, $name);
});

it('handles cloning failure', function () {
    $vendor = 'redberry';
    $name = 'example';
    $packagePath = "/packages/{$vendor}/{$name}";
    $packageUrl = 'https://github.com/laravel/skeleton.git';

    // Mock filesystem
    $this->filesystem->shouldReceive('exists')
        ->with($packagePath)
        ->once()
        ->andReturn(false);

    // Mock cloner to throw
    $this->cloner->shouldReceive('clone')
        ->with($packagePath, $packageUrl, 'main', true)
        ->once()
        ->andThrow(new \Exception('Clone failed'));

    // Expect no commands
    $this->commandRunner->shouldNotReceive('runInteractive');

    $this->composerUpdater->shouldReceive('addRepository');

    // Expect exception
    expect(fn () => $this->initiator->initialize($vendor, $name))
        ->toThrow(\Exception::class, 'Clone failed');
});

it('validates empty vendor or name', function () {
    $packagePath = '/packages//example';
    $packageUrl = 'https://github.com/laravel/skeleton.git';

    // Mock filesystem
    $this->filesystem->shouldReceive('exists')
        ->with($packagePath)
        ->once()
        ->andReturn(false);

    // Mock cloner
    $this->cloner->shouldReceive('clone')
        ->with($packagePath, $packageUrl, 'main', true)
        ->once();

    // Mock commands
    $this->commandRunner->shouldReceive('runInteractive')
        ->with("cd {$packagePath} && composer install")
        ->once();
    $this->commandRunner->shouldReceive('runInteractive')
        ->with("cd {$packagePath} && npm install")
        ->once();

    $this->composerUpdater->shouldReceive('addRepository')->once();

    // Should not throw, but path will have empty vendor
    $this->initiator->initialize('', 'example');
});

it('handles special characters in vendor and name', function () {
    $vendor = 'redberry-123';
    $name = 'example_456';
    $packagePath = "/packages/{$vendor}/{$name}";
    $packageUrl = 'https://github.com/laravel/skeleton.git';

    // Mock filesystem
    $this->filesystem->shouldReceive('exists')
        ->with($packagePath)
        ->once()
        ->andReturn(false);

    // Mock cloner
    $this->cloner->shouldReceive('clone')
        ->with($packagePath, $packageUrl, 'main', true)
        ->once();

    // Mock commands
    $this->commandRunner->shouldReceive('runInteractive')
        ->with("cd {$packagePath} && composer install")
        ->once();
    $this->commandRunner->shouldReceive('runInteractive')
        ->with("cd {$packagePath} && npm install")
        ->once();

    $this->composerUpdater->shouldReceive('addRepository');

    // Should handle special characters without issues
    $this->initiator->initialize($vendor, $name);
});
