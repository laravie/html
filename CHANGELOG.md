# Changelog

This changelog references the relevant changes (bug and security fixes) done to `laravie/html`.

## 5.4.0

Released: 2017-01-27

### Changes

* Update support to Laravel Framework 5.4.
* Added support for array attributes. ([@guillaumebriday](https://github.com/guillaumebriday))

## 5.3.1

Released: 2016-11-27

### Changes

* Allow to escape `Form::button()`.

## 5.3.0

Released: 2016-08-26

### Changes

* Update support to Laravel Framework 5.3.

## 5.2.3

Released: 2016-06-21

### Fixed

* `_method` fields shouldn't use session data. This solves scenario where Form is used more than once.

### Changes

* Make escaping of labels for HTML and mailto links optional.
* Transform the field name when getting the form value from model.
* Allow `Collective\Html\Eloquent\FormAccessible` to access value from related model.

## 5.2.2

Released: 2016-02-16

### New

* Add `Collective\Html\HtmlBuilder::tag()` helper. ([@paulvl](https://github.com/paulvl))

### Fixed

* `Collective\Html\HtmlBuilder::textarea()` should return `Illuminate\Support\HtmlString`.

## 5.2.1

Released: 2015-12-27

### New

* Add `native-placeholder` configuration option to display native placeholder for `Form::select()`.

## 5.2.0

Released: 2015-12-22

### New

* Add `Collective\Html\Componentable` trait to build HTML or Form components. ([@adamgoose](https://github.com/adamgoose)) 
* Add `Collective\Html\Eloquent\FormAccessible` trait. [@adamgoose](https://github.com/adamgoose)) 

### Changes

* Update support to Laravel Framework 5.2.
* Convert all output to return `Illuminate\Support\Htmlable` instead of basic `string`. [@adamgoose](https://github.com/adamgoose)) 

## 5.1.5

Released: 2015-12-27

### New

* Add `native-placeholder` configuration option to display native placeholder for `Form::select()`.

## 5.1.4

Released: 2015-12-21

### New

* Add `Form::color()` helper method. ([@mul14](https://github.com/mul14))
* Add boolean attributes unit tests. ([@EspadaV8](https://github.com/EspadaV8))
* Add select optgroup unit tests. ([@EspadaV8](https://github.com/EspadaV8))

## 5.1.3

Released: 2015-11-25

### New

* Add documentation.

### Changes

* Support boolean HTML attributes. `Form::text('foo', null, ['required'])` should now return `<input type="text" name="foo" required>`.

### Fixed

* Properly populate select from Collection in Laravel 5.1.

## 5.1.2

Released: 2015-11-19

### New

* Add `Form::datetime()` helper method.
* Add `Form::datetimeLocal()` helper method.

## 5.1.1

Released: 2015-11-06

### Changes

* Remove requirement to assign CSRF token from the Service Provider as it might be loaded before session middleware is ready.
* Don't depends on concretes `UrlGenerator` in `Collective\Html\FormBuilder`.
* `HTML::dl()` now supports multiple descriptions per term.
* first character capitalization in month names for `Form::selectMonth()`.
 
## 5.1.0

Released: 2015-11-02

### New

* Fork the base HTML package from <https://github.com/laravelcollective/html>.
* Add following traits:
  - `Collective\Html\Traits\CheckerTrait`
  - `Collective\Html\Traits\CreatorTrait`
  - `Collective\Html\Traits\InputTrait`
  - `Collective\Html\Traits\ObfuscateTrait`
  - `Collective\Html\Traits\SelectionTrait` 
  - `Collective\Html\Traits\SessionHelperTrait` 


