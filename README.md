# Lumen-Modules

| **Laravel**  |  **lumen-modules** |
|---|---|

| 7.0  | ^7.0  |

`iurylippo/lumen-modules` lumen package to generate modules with controllers, providers, services, repositories,
entities and tests in a Dependency Injected way.

## Install

To install through Composer, by run the following command:

``` bash
composer require iurylippo/lumen-modules
```

### Autoloading

By default the module classes are not loaded automatically. You can autoload your modules using `psr-4`. For example:

``` json
{
  "autoload": {
    "psr-4": {
      "App\\": "app/",
      "Modules\\": "Modules/"
    }
  }
}
```

**Tip: don't forget to run `composer dump-autoload` afterwards.**

Lumen doesn't come with a vendor publisher. In order to use laravel-modules with lumen you have to set it up manually.

Create a config folder inside the root directory and copy `vendor/nwidart/laravel-modules/config/config.php` to that folder named `modules.php`

``` bash
mkdir config
cp vendor/nwidart/laravel-modules/config/config.php config/modules.php
```

Then load the config and the service provider in`bootstrap/app.php`

``` bash
$app->configure('modules');
$app->register(\Nwidart\Modules\LumenModulesServiceProvider::class);
```
Laravel-modules uses path.public which isn't defined by default in Lumen.
Register path.public before loading the service provider.

``` bash
$app->bind('path.public', function() {
 return __DIR__ . 'public/';
});
```

**Tip: Put this path bind above the $app.**

## Documentation

You'll find installation instructions and full documentation on [https://nwidart.com/laravel-modules/](https://nwidart.com/laravel-modules/). THE ORIGINAL PACKAGE FOR LARAVEL

## Credits

- [Nicolas Widart](https://github.com/nwidart)

## About Nicolas Widart

Nicolas Widart is a freelance web developer specialising on the Laravel framework. View all the packages [on my website](https://nwidart.com/), or visit [my website](https://nicolaswidart.com).


## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
