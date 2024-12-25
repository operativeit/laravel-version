# Version management package for Laravel

This is a [SemVer](http://semver.org) compatible version management package for any software built on Laravel.

## Getting Started

### 1. Install

Run the following command:

```bash
composer require eom-plus/laravel-version
```

### 2. Publish

Publish config file.

```bash
php artisan vendor:publish --tag=version
```

### 3. Configure

You can change the version information of your app from `config/version.php` file

## Usage

### version($method = null)

You can either enter the method like `version('short')` or leave it empty so you could firstly get the instance then call the methods like `version()->short()`

## Changelog

Please see [Releases](../../releases) for more information what has changed recently.

## Contributing

Pull requests are more than welcome. You must follow the PSR coding standards.

## Security

If you discover any security related issues, please send us an email to oss@operativeit.es instead of using the issue tracker.

## Credits

- [Operative IT](https://github.com/operativeit)
- [All Contributors](../../contributors)

## License

The MIT License (MIT). Please see [LICENSE](LICENSE.md) for more information.
