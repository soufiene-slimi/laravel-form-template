# Laravel From Template

[![Latest Version on Packagist](https://img.shields.io/packagist/v/soufiene-slimi/laravel-form-template.svg)](https://packagist.org/packages/soufiene-slimi/laravel-form-template)
[![Quality Score](https://img.shields.io/scrutinizer/g/soufiene-slimi/laravel-form-template.svg)](https://scrutinizer-ci.com/g/soufiene-slimi/laravel-form-template)
[![Total Downloads](https://img.shields.io/packagist/dt/soufiene-slimi/laravel-form-template.svg)](https://packagist.org/packages/soufiene-slimi/laravel-form-template)

This laravel package allow you to save some form templates to apply them whenever you want. the utility is to avoid filling some inputs again and again, that most of the time have the same values, or maybe to apply some template based on the user choice.

## Installation

You can install the package via composer:

```bash
composer require soufiene-slimi/laravel-form-template
```

## Usage

First of all you don't need to import anything since the package provide and alias.

### Creating a new template

``` php
// creating a new template named 'Template 1' with
// data ['name' => 'foo','email' => 'foo@administrator.com']
Template::make('Template 1', [
    'name' => 'foo',
    'email' => 'foo@administrator.',
]);
// creating a new template named 'Template 1' using a model instance
Template::makeForModel('Template 1', $user);
// creating a new template named 'Template 1' using a model instance
// and some extra data
Template::makeForModel('Template 1', $user, ['status_id' => 2]);
// creating a new template named 'Template 1' using a model instance,
//some extra data, and keeping all the model attributes
Template::makeForModel('Template 1', $user, ['foo' => 'bar'], false);
```
> Note that you can configure the attributes that will be removed from a model during the template creation by publishing the configuration and updating the **excluded** key.

### Applying a template
To apply a template, first you have to use the **old()** function in your input:
``` html
<input type="text" name="name" value="{{ old('name') }}" />
```
Then find the template and apply it

``` php
Template::first()->apply();
```

> Template is an instance of the **Illuminate\Database\Eloquent\Model**, this mean that you can use all the **Laravel ORM** features.

To publish the configuration file run:
```bash
php artisan vendor:publish  --provider=SoufieneSlimi\LaravelFormTemplate\LaravelFormTemplateServiceProvider
```

### Changelog

Please see [CHANGELOG](CHANGELOG.md) for more information what has changed recently.

## Contributing

Please see [CONTRIBUTING](CONTRIBUTING.md) for details.

### Security

If you discover any security related issues, please email soufiene.slimi@mail.com instead of using the issue tracker.

## Credits

- [Soufiene Slimi](https://github.com/soufiene-slimi)
- [All Contributors](../../contributors)

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
