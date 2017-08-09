# IBAN for PHP

A PHP implementation for IBAN value object.

[![Build Status](https://travis-ci.org/xafardero/generate-iban.svg?branch=master)](https://travis-ci.org/xafardero/generate-iban)
[![StyleCI](https://styleci.io/repos/73861559/shield)](https://styleci.io/repos/73861559)
[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/xafardero/generate-iban/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/xafardero/generate-iban/?branch=master)
[![Code Coverage](https://scrutinizer-ci.com/g/xafardero/generate-iban/badges/coverage.png?b=master)](https://scrutinizer-ci.com/g/xafardero/generate-iban/?branch=master)

## Usage

```php
// Generate a Spanish IBAN
$iban = Iban::fromString('ES7809895990446462241825');

```

## Requirements

PHP is required to be compiled with "--enable-bcmath" for some arbitrary precision mathematic checks (IBAN & BBAN).

## Installing

### Via GitHub

```bash
$ git clone git@github.com:xafardero/generate-iban.git
```

Autoloading is PSR-0 friendly.

### Via [Packagist](https://packagist.org/packages/xafardero/generate-iban) & [Composer](https://getcomposer.org/doc/00-intro.md)

Require the latest version of `xafardero/generate-iban` with Composer

```bash
$ composer require xafardero/generate-iban
```
