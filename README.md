# Germania KG · Databases

**[Pimple Service Provider](https://pimple.symfony.com/#extending-a-container) for creating [PDO handlers.](http://php.net/manual/en/pdo.construct.php)**


[![Build Status](https://travis-ci.org/GermaniaKG/Databases.svg?branch=master)](https://travis-ci.org/GermaniaKG/Databases)
[![Code Coverage](https://scrutinizer-ci.com/g/GermaniaKG/Databases/badges/coverage.png?b=master)](https://scrutinizer-ci.com/g/GermaniaKG/Databases/?branch=master)
[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/GermaniaKG/Databases/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/GermaniaKG/Databases/?branch=master)
[![Build Status](https://scrutinizer-ci.com/g/GermaniaKG/Databases/badges/build.png?b=master)](https://scrutinizer-ci.com/g/GermaniaKG/Databases/build-status/master)


## Installation

```bash
$ composer require germania-kg/databases
```

## Setup

```php
<?php
use Germania\Databases\DatabasesServiceProvider;

// A. Use with Slim or Pimple
$app = new \Slim\App;
$dic = $app->getContainer();
$dic = new Pimple\Container;

// B. Register Service Provider.
// see https://pimple.symfony.com/#extending-a-container
// Optionally pass custom PDO error mode
$dic->register( new DatabasesServiceProvider );
$dic->register( new DatabasesServiceProvider( \PDO::ERRMODE_EXCEPTION ) );
```


## Services

### PDO.Factory

Factory callable for PDO handlers. Just have some database credentials at hand. 

```php
<?php
$db = [
	'dsn' => "mysql:host=localhost;dbname=MyDatabase;charset=utf8",
	'user' => "username",
	'pass' => "secret"
];

// Grab Factory
$pdo_factory = $dic['PDO.Factory'];

// Create handler
$pdo = $pdo_factory( $db );
$pdo = $pdo_factory( (object) $db ); // StdClass objects
$pdo = $pdo_factory( new \ArrayObject($db) ); // ArrayAccess instance
```

#### Exceptions
The factory accepts an *array*, *ArrayAccess* instance or a *StdClass* object. If the factory parameter passed does not match any of these, an **\InvalidArgumentException** will be thrown

### PDO.ErrorMode

The default error mode has been set on Service provider instantiation. You may override it at runtime by extending the service definition:

```php
$dic->extend(PDO.ErrorMode', function($default_error_mode, $dic) {
    return \PDO::ERRMODE_SILENT;
});
```

### PDO.Options

This service returns the PDO options to use on PDO handler instantiation. By default this is an array with the *PDO.ErrorMode* shown above. 

```php
$pdo_options = $dic['PDO.Options'];
```

### Overriding
Just extend the service definition. See [PHP manual: class PDO](http://php.net/manual/en/pdo.construct.php) for valid options.

```php
$dic->extend('PDO.Options', function($default_options, $dic) {
    return array_merge( $default_options, array(
    	\PDO::ATTR_ERRMODE => \PDO::ERRMODE_SILENT
    	// custom values here
    ));
});

```


## Unit tests

Either copy `phpunit.xml.dist` to `phpunit.xml` and adapt to your needs, or leave as is. 
Run [PhpUnit](https://phpunit.de/) like this:

```bash
$ vendor/bin/phpunit
```

