<?php

use Illuminate\Filesystem\Filesystem;
use Mockery as m;
use Redberry\LaravelPackageInit\ComposerJsonUpdater;

beforeEach(function () {
    $this->filesystem = m::mock(Filesystem::class);
    $this->updater = new ComposerJsonUpdater($this->filesystem);
    $this->composerPath = base_path('composer.json');
});

afterEach(function () {
    m::close();
});

it('adds repository and require-dev for new package', function () {
    $vendor = 'acme';
    $name = 'example';
    $packagePath = "/packages/{$vendor}/{$name}";
    $packageName = "{$vendor}/{$name}";

    $initialComposer = [
        'name' => 'laravel/laravel',
        'require' => [],
    ];

    $this->filesystem->shouldReceive('exists')
        ->with($this->composerPath)
        ->once()
        ->andReturn(true);
    $this->filesystem->shouldReceive('get')
        ->with($this->composerPath)
        ->once()
        ->andReturn(json_encode($initialComposer));
    $this->filesystem->shouldReceive('put')
        ->with($this->composerPath, m::type('string'))
        ->once()
        ->andReturnUsing(function ($path, $content) use ($packagePath, $packageName) {
            $decoded = json_decode($content, true);
            expect($decoded['repositories'])
                ->toContain([
                    'type' => 'path',
                    'url' => './'.$packagePath,
                    'options' => ['symlink' => true],
                ])
                ->and($decoded['require-dev'])
                ->toHaveKey($packageName, '*');
        });

    $this->updater->addRepository($packagePath, $vendor, $name);
});

it('does not duplicate repository or require-dev', function () {
    $vendor = 'acme';
    $name = 'example';
    $packagePath = "/packages/{$vendor}/{$name}";
    $packageName = "{$vendor}/{$name}";

    $initialComposer = [
        'name' => 'laravel/laravel',
        'repositories' => [
            [
                'type' => 'path',
                'url' => '.'.$packagePath,
                'options' => ['symlink' => true],
            ],
        ],
        'require-dev' => [
            $packageName => '*',
        ],
    ];

    $this->filesystem->shouldReceive('exists')
        ->with($this->composerPath)
        ->once()
        ->andReturn(true);
    $this->filesystem->shouldReceive('get')
        ->with($this->composerPath)
        ->once()
        ->andReturn(json_encode($initialComposer));
    $this->filesystem->shouldNotReceive('put');

    $this->updater->addRepository($packagePath, $vendor, $name);
});
