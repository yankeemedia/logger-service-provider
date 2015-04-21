# Logger Service Provider
[![Build Status](https://img.shields.io/travis/dafiti/logger-service-provider/master.svg?style=flat-square)](https://travis-ci.org/dafiti/logger-service-provider)
[![Scrutinizer Code Quality](https://img.shields.io/scrutinizer/g/dafiti/logger-service-provider/master.svg?style=flat-square)](https://scrutinizer-ci.com/g/dafiti/logger-service-provider/?branch=master)
[![Code Coverage](https://img.shields.io/scrutinizer/coverage/g/dafiti/logger-service-provider/master.svg?style=flat-square)](https://scrutinizer-ci.com/g/dafiti/logger-service-provider/?branch=master)
[![HHVM](https://img.shields.io/hhvm/dafiti/logger-service-provider.svg?style=flat-square)](https://travis-ci.org/dafiti/logger-service-provider)
[![Latest Stable Version](https://img.shields.io/packagist/v/dafiti/logger-service-provider.svg?style=flat-square)](https://packagist.org/packages/dafiti/logger-service-provider)
[![Total Downloads](https://img.shields.io/packagist/dt/dafiti/logger-service-provider.svg?style=flat-square)](https://packagist.org/packages/dafiti/logger-service-provider)
[![License](https://img.shields.io/packagist/l/dafiti/logger-service-provider.svg?style=flat-square)](https://packagist.org/packages/dafiti/logger-service-provider)

An extended Logger Service Provider for [Silex](https://github.com/silexphp/silex) based on [Monolog](https://github.com/Seldaek/monolog)

## Instalation
The package is available on [Packagist](http://packagist.org/packages/dafiti/logger-service-provider).
Autoloading is [PSR-4](https://github.com/php-fig/fig-standards/blob/master/accepted/PSR-4-autoloader.md) compatible.

```json
{
    "require": {
        "dafiti/logger-service-provider": "dev-master"
    }
}
```


## Usage

```php
use Silex\Application;
use Dafiti\Silex\LoggerServiceProvider;

$app = new Application();
$app->register(new LoggerServiceProvider(), [
    'logger.log_folder' => 'data/logs/',
    'logger.level'      => 'debug'
]);

// Create Logger - (StreamHandler default)
$app['logger.factory']('app');

// Create Logger with another handlers
$app['logger.factory']('worker', 'info', [
    new FirePHPHandler(),
    new ErrorLogHandler(ErrorLogHandler::OPERATING_SYSTEM)
]);

// Create Logger with processors
$app['logger.factory']('worker', 'info', [], [
    new Processor\UidProcessor()
]);

// Log something
$app['logger']->get('worker')->log('something');
$app['logger']->worker->log('something');


// Add customer logger

class Custom extends \Dafiti\Silex\Log\Logger
{
}

$app['logger']->add(new Custom('custom'));

// Check if logger exists
$app['logger']->has('worker'); //boolean
```

## License

MIT License
