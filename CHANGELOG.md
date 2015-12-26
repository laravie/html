# Changelog

This changelog references the relevant changes (bug and security fixes) done to `html`.

## 5.1.5 - 2015-12-27

### New

* Add `native-placeholder` configuration option to display native placeholder for `Form::select()`.

## 5.1.4 - 2015-12-21

### New

* Add `Form::color()` helper method. ([@mul14](https://github.com/mul14))
* Add boolean attributes unit tests. ([@EspadaV8](https://github.com/EspadaV8))
* Add select optgroup unit tests. ([@EspadaV8](https://github.com/EspadaV8))

## 5.1.3 - 2015-11-25

### New

* Add documentation.

### Changes

* Support boolean HTML attributes. `Form::text('foo', null, ['required'])` should now return `<input type="text" name="foo" required>`.

### Fixed

* Properly populate select from Collection in Laravel 5.1.

## 5.1.2 - 2015-11-19

### New

* Add `Form::datetime()` helper method.
* Add `Form::datetimeLocal()` helper method.

## 5.1.1 - 2015-11-06

### Changes

* Remove requirement to assign CSRF token from the Service Provider as it might be loaded before session middleware is ready.
* Don't depends on concretes `UrlGenerator` in `Collective\Html\FormBuilder`.
* `HTML::dl()` now supports multiple descriptions per term.
* first character capitalization in month names for `Form::selectMonth()`.
 
## 5.1.0 - 2015-11-02

### New

* Fork the base HTML package from <https://github.com/laravelcollective/html>.
* Add following traits:
  - `Collective\Html\Traits\CheckerTrait`
  - `Collective\Html\Traits\CreatorTrait`
  - `Collective\Html\Traits\InputTrait`
  - `Collective\Html\Traits\ObfuscateTrait`
  - `Collective\Html\Traits\SelectionTrait` 
  - `Collective\Html\Traits\SessionHelperTrait` 


