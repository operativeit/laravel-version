# Version management package for Laravel

This is a [SemVer](http://semver.org) compatible version management package for any software built on Laravel.

## Getting Started

### 1. Install

Run the following command:

```bash
composer require eom-plus/laravel-version
```

## Usage

### init

```bash
php artisan version:init
```

### set new version

Set new version using semver format, eg 1.0.0

```bash
php artisan version <newversion>
```

### increment version patch/min/major

Increment version number

```bash
php artisan version [ patch | minor | major ]
```

### commit release to git 

Create and commit git tag for the current release

```bash
php artisan version commit
```

Create github workflow to create release automatically.

* Create directory .github/workflows in your git repository
* Copy our file .github/workflows/release.yml to your .github/workflows
* On your the next commit, a release will be created automatically.


### helpers

version() , returns current version
codename(), return current codename

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
