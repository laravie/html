# Changelog

This changelog references the relevant changes (bug and security fixes) done to `laravie/html`.

## 5.8.0

Released: 2019-02-26

### Changes

* Update support to Laravel Framework 5.8.
* Improve performance by prefixing all global functions calls with `\` to skip the look up and resolve process and go straight to the global function. 

## 5.7.1

Released: 2019-02-19

### Changes

* Simplify `hasFormMutator` method implementation to check against `method_exists` instead of using `ReflectionClass`.
* Force `placeholder` is not selectable on `<select>`.
* Allows `'0'` as text on `<label>`.

## 5.7.0

Released: 2017-09-05

### Changes

* Update support to Laravel Framework 5.7. 

## 5.6.4

Released: 2018-08-02

### Changes

* Allow `Request` instance to be optional. ([@decadence](https://github.com/decadence))
* Allow to set `class` attributes using `iterable` data. ([@ThunderBirdsX3](https://github.com/ThunderBirdsX3))
* Allow sub-select to accept `iterable` data. ([@tautvydasr](https://github.com/tautvydasr))

## 5.6.3

Released: 2018-05-24

### Added

* Added `range()`, `week()` and `month()` to `FormBuilder`.

### Fixes

* Fixes `time()` to accept `DateTime` instance.

## 5.6.2

Released: 2018-04-14

### Fixes

* Fix issue where defaults has a higher priority then the attributes. ([@
b3a1024](https://github.com/laravie/html/commit/b3a10245c791a211e5f8ec37117f4549cd22aabe))
* Add missing `Illuminate\Contracts\Support\Htmlable` import. ([@c1f1999](https://github.com/laravie/html/commit/c1f1999b02cdd5aebe351428909fd2e21ad2176a))

## 5.6.1

Released: 2018-04-03

### Changes

* Validation in select field for boolean value of selected option. ([@AndersonFriaca](https://github.com/AndersonFriaca))
* Placeholder for select element is no longer hidden. ([@ilyadrv](https://github.com/ilyadrv))

### Fixes

* Fix form method of hidden input '_method' is preserved on next request. ([@tortuetorche](https://github.com/tortuetorche))
* Fix bug with radio button not being checked. ([@devinfd](https://github.com/devinfd))
* Fix selected to check for using `Illuminate\Support\Collection::contains()` whenever possible. ([@VinceG](https://github.com/VinceG))

## 5.6.0

Released: 2018-02-12

### Added

* Add `Collective\Html\FormBuilder::getModel()` method. ([@thetar](https://github.com/thetar))

### Changes

* Update support to Laravel Framework 5.6.
* Use strict comparison to check selected values when building select options. ([@muvasco](https://github.com/muvasco))
* Form method of hidden input '_method' is preserved on next request. ([@tortuetorche](https://github.com/tortuetorche))
* Allows `Illuminate\Http\Request` to be optional. (Victor Isadov)
* Replace `+` operator with `array_merge()` method. ([@izniburak](https://github.com/izniburak))
* Remove `PHP_EOL` statements from HTML output. ([@nickurt](https://github.com/nickurt))

## 5.5.2

Released: 2018-04-03

### Changes

* Validation in select field for boolean value of selected option. ([@AndersonFriaca](https://github.com/AndersonFriaca))

### Fixes

* Fix form method of hidden input '_method' is preserved on next request. ([@tortuetorche](https://github.com/tortuetorche))
* Fix selected to check for using `Illuminate\Support\Collection::contains()` whenever possible. ([@VinceG](https://github.com/VinceG))

## 5.5.1

Released: 2018-02-10

### Added

* Add `Collective\Html\FormBuilder::getModel()` method. ([@thetar](https://github.com/thetar))

### Changes

* Use strict comparison to check selected values when building select options. ([@muvasco](https://github.com/muvasco))
* Form method of hidden input '_method' is preserved on next request. ([@tortuetorche](https://github.com/tortuetorche))
* Allows `Illuminate\Http\Request` to be optional. (Victor Isadov)
* Replace `+` operator with `array_merge()` method. ([@izniburak](https://github.com/izniburak))
* Remove `PHP_EOL` statements from HTML output. ([@nickurt](https://github.com/nickurt))

## 5.5.0

Released: 2017-08-31

### Changes

* Update support to Laravel Framework 5.5.


