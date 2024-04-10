# Versioning

[![Latest Version on Packagist](https://img.shields.io/packagist/v/williamug/versioning.svg?style=flat-square)](https://packagist.org/packages/williamug/versioning/stats#major/all)
[![Total Downloads](https://img.shields.io/packagist/dt/williamug/versioning.svg?style=flat-square)](https://packagist.org/packages/williamug/versioning/stats)

A PHP package to helps you to display the version of your application by applying git version tags

## Installation

You can install the package via composer:

```bash
composer require williamug/versioning
```


## Usage

#### For Vanilla PHP
If your project is written in vanilla PHP you can use the following code to display the version of your application:
```php

require __DIR__ . '/vendor/williamug/versioning/src/functions.php';

// after requiring the function file you can now use the app_versioning() function to display the version of your application
app_versioning();
//v1.0.0
```

#### For Laravel
If you are using Laravel you can use the following code to display the version of your application:

```php
Williamug\Versioning\Versioning::tag()
// v1.0.0
```
You can add the above piece of code in either your controller or blade views.

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

- [Asaba William](https://github.com/williamug)
- [All Contributors](../../contributors)

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
