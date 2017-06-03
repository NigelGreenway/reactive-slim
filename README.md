[![Latest Version](https://img.shields.io/github/release/NigelGreenway/reactive-slim.svg?style=flat-square)](https://github.com/NigelGreenway/reactive-slim/releases)
[![Software License][ico-license]](LICENSE.md)
[![Build Status][ico-travis]][link-travis]
[![Total Downloads][ico-downloads]][link-downloads]

# README

Reactive Slim is a bridge where you can pass your [Slim](https://www.slimframework.com/) instance and reap the benefit's of [ReactPHP](http://reactphp.org/)'s event driven, non-blocking I/O usage of PHP.

The implementation run's on PHP7.0 and makes good use of PHP's [Scalar Type hints](http://php.net/manual/en/functions.arguments.php#functions.arguments.type-declaration).  

## Installation

`composer require nigelgreenway/reactive-slim`

## Usage

After creating your [Slim Instance](https://www.slimframework.com/), pass it to the following construction:

```php
(new \ReactiveSlim\Server($slimInstance))
    ->run();
```

_Please see the [examples](/example) for more information or run `php ./example/app-dev-mode.php`._

If are you are running via PHP locally, you are able to pass the following flags to customise both the host and the port:

`-h 0.0.0.0` or `--host=0.0.0.0` and `-p 8686` or `--port=8686`

### Restarting your ReactApp

There are 3 ways to restart your application (only tested on Linux - Solus, kernel 4.9.30-29.lts currently):

 - `sh ./.tools/local-watch` runs via local PHP installation (requires `inotify-tools` to be installed via package manager)
 - `sh ./.tools/reactor` run via local PHP installation and requires the `reactor.config.json` config
 - `sh ./.tools/docker-watch` run the app within a Docker container (requires `inotify-tools` to be installed via package manager)

### Extra options

`#withHost(<string>)` - The default host URL is `0.0.0.0` but is overridden by passing a string as the parameter

`#withPort(<int>)` - The default port is `1337` but is overridden by passing an integer as the parameter 

`#withEnvironment(<int>)` - The default is `0` which is the `Production` environment, the full options are:
 
  - `ServerEnvironment::PRODUCTION` (0)
  - `ServerEnvironment::STAGING` (1)
  - `ServerEnvironment::TESTING` (2)
  - `ServerEnvironment::DEVELOPMENT` (3)
  
## Change log

Please see [CHANGELOG](CHANGELOG.md) for more information on what has changed recently.

## Testing

``` bash
$ composer test
```

## Contributing

Please see [CONTRIBUTING](CONTRIBUTING.md) and [CONDUCT](CONDUCT.md) for details.

## Security

If you discover any security related issues, please email :author_email instead of using the issue tracker.

## Credits

- [Nigel Greenway](http://github.com/NigelGreenway)
- [All Contributors](link-contributors)

This has been created to plug two great packages together:
- [SlimPHP](https://github.com/slimphp/Slim/graphs/contributors)
- [ReactPHP](https://github.com/reactphp/react/graphs/contributors)

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.


[ico-license]: https://img.shields.io/badge/license-MIT-brightgreen.svg?style=flat-square
[ico-travis]: https://img.shields.io/travis/NigelGreenway/reactive-slim/master.svg?style=flat-square
[ico-downloads]: https://img.shields.io/packagist/dt/NigelGreenway/reactive-slim.svg?style=flat-square

[link-packagist]: https://packagist.org/packages/NigelGreenway/reactive-slim
[link-travis]: https://travis-ci.org/NigelGreenway/reactive-slim
[link-downloads]: https://packagist.org/packages/NigelGreenway/reactive-slim