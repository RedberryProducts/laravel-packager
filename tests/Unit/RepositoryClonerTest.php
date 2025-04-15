<?php

declare(strict_types=1);

namespace Tests\Unit;

use Illuminate\Filesystem\Filesystem;
use Mockery;
use Redberry\LaravelPackager\CommandRunner;
use Redberry\LaravelPackager\RepositoryCloner;
use RuntimeException;

afterEach(function () {
    Mockery::close();
});

it('clones repository without unlinking the .git directory', function () {
    $path = '/var/www/package';
    $url = 'https://github.com/some/repo';
    $branch = 'main';

    // Create a mock for Filesystem and expect deleteDirectory NOT to be called.
    $filesystem = Mockery::mock(Filesystem::class);
    $filesystem->shouldNotReceive('deleteDirectory');

    // Create a mock for CommandRunner; expect a specific git clone command.
    $commandRunner = Mockery::mock(CommandRunner::class);
    $expectedCommand = "git clone --branch {$branch} {$url} {$path}";
    $commandRunner->shouldReceive('run')
        ->once()
        ->with($expectedCommand)
        ->andReturnNull();

    $cloner = new RepositoryCloner($filesystem, $commandRunner);
    $cloner->clone($path, $url);
});

it('clones repository and deletes the .git directory when unlink is true', function () {
    $path = '/var/www/package';
    $url = 'https://github.com/some/repo';
    $branch = 'main';

    // Create a mock for Filesystem and expect deleteDirectory to be called with the proper .git path.
    $filesystem = Mockery::mock(Filesystem::class);
    $filesystem->shouldReceive('deleteDirectory')
        ->once()
        ->with($path.'/.git')
        ->andReturnTrue();

    // Create a mock for CommandRunner; expect a specific git clone command.
    $commandRunner = Mockery::mock(CommandRunner::class);
    $expectedCommand = "git clone --branch {$branch} {$url} {$path}";
    $commandRunner->shouldReceive('run')
        ->once()
        ->with($expectedCommand)
        ->andReturnNull();

    $cloner = new RepositoryCloner($filesystem, $commandRunner);
    $cloner->clone($path, $url, $branch, true);
});

it('clones repository using a different branch', function () {
    $path = '/var/www/package';
    $url = 'https://github.com/some/repo';
    $branch = 'develop';

    // Filesystem should not be used to delete the .git folder when unlink is false.
    $filesystem = Mockery::mock(Filesystem::class);
    $filesystem->shouldNotReceive('deleteDirectory');

    // CommandRunner must be called with the non-default branch.
    $commandRunner = Mockery::mock(CommandRunner::class);
    $expectedCommand = "git clone --branch {$branch} {$url} {$path}";
    $commandRunner->shouldReceive('run')
        ->once()
        ->with($expectedCommand)
        ->andReturnNull();

    $cloner = new RepositoryCloner($filesystem, $commandRunner);
    $cloner->clone($path, $url, $branch, false);
});

it('propagates exception from CommandRunner and does not delete .git directory', function () {
    $path = '/var/www/package';
    $url = 'https://github.com/some/repo';
    $branch = 'main';

    // Create a Filesystem mock that should never call deleteDirectory.
    $filesystem = Mockery::mock(Filesystem::class);
    $filesystem->shouldNotReceive('deleteDirectory');

    // Create a CommandRunner mock that throws an exception.
    $commandRunner = Mockery::mock(CommandRunner::class);
    $expectedCommand = "git clone --branch {$branch} {$url} {$path}";
    $commandRunner->shouldReceive('run')
        ->once()
        ->with($expectedCommand)
        ->andThrow(new RuntimeException('Clone failed'));

    $cloner = new RepositoryCloner($filesystem, $commandRunner);

    // Expect the clone() method to propagate the exception.
    $cloner->clone($path, $url);
})->throws(RuntimeException::class, 'Clone failed');
