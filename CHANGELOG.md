# Changelog

This changelog references the relevant changes (bug and security fixes) done to `laravie/html`.

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


