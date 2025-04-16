# Package to easily initiate new package using spatie's laravel package skeleton

[![Latest Version on Packagist](https://img.shields.io/packagist/v/redberryproducts/laravel-packager.svg?style=flat-square)](https://packagist.org/packages/redberryproducts/laravel-packager)
[![GitHub Tests Action Status](https://img.shields.io/github/actions/workflow/status/redberryproducts/laravel-packager/run-tests.yml?branch=main&label=tests&style=flat-square)](https://github.com/redberryproducts/laravel-packager/actions?query=workflow%3Arun-tests+branch%3Amain)
[![GitHub Code Style Action Status](https://img.shields.io/github/actions/workflow/status/redberryproducts/laravel-packager/fix-php-code-style-issues.yml?branch=main&label=code%20style&style=flat-square)](https://github.com/redberryproducts/laravel-packager/actions?query=workflow%3A"Fix+PHP+code+style+issues"+branch%3Amain)
[![Total Downloads](https://img.shields.io/packagist/dt/redberryproducts/laravel-packager.svg?style=flat-square)](https://packagist.org/packages/redberryproducts/laravel-packager)

Laravel Packager is a streamlined Laravel package designed to simplify the creation of new package skeletons within your project. With minimal effort, it clones a customizable skeleton repository, detaches it from its original Git history, and seamlessly integrates the new package into your application’s composer.json. By automating these steps, Laravel Packager eliminates the friction of package initialization, making it effortless to extract reusable features into standalone packages for enhanced modularity and maintainability.

## Installation

You can install the package via composer:

```bash
composer require redberryproducts/laravel-packager
```

You can publish the config file with:

```bash
php artisan vendor:publish --tag="packager-config"
```

This is the contents of the published config file:

```php
return [
    'default_skeleton' => 'spatie',
    'skeletons' => [
        'spatie' => [
            'url' => 'https://github.com/spatie/package-skeleton-laravel.git',
            'branch' => 'main',
            'runs' => [
                'php configure.php',
            ],
        ],
    ],
    'packages_directory' => 'packages',
];
```

If you want to add another skeleton, or create your custom skeleton and give package ability to initilize this inside your project, you will need to do the following:
1. Create a package skeleton github repository
2. Add new entry to *skeletons* list inside the published config
3. Modify *default_skeletons* to reflect new skeleton name

## Usage

```bash
php artisan make:package vendor-name/package-name
```
For example:
```bash
php artisan make:package acme/example
```

This command:
Clones the Spatie skeleton (or your configured skeleton) to packages/acme/example.

Initializes a new Git repository, detaching it from the skeleton’s history.

Updates composer.json to include:
```json
{
    "repositories": [
        {
            "type": "path",
            "url": "./packages/acme/example",
            "options": {
                "symlink": true
            }
        }
    ],
    "require-dev": {
        "acme/example": "*"
    }
}
```

## Testing

```bash
composer test
```

## Changelog

Please see [CHANGELOG](CHANGELOG.md) for more information on what has changed recently.

## Contributing

Please see [CONTRIBUTING](CONTRIBUTING.md) for details.

## Security Vulnerabilities

Please review [our security policy](../../security/policy) on how to report security vulnerabilities.

## Credits

- [Nika Jorjoliani](https://github.com/RedberryProducts)
- [All Contributors](../../contributors)

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
