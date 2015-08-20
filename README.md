invoiced-php
========

This repository contains the PHP client library for the [Invoiced](https://invoiced.com) API.

[![Build Status](https://travis-ci.org/Invoiced/invoiced-php.svg?branch=master)](https://travis-ci.org/Invoiced/invoiced-php)
[![Coverage Status](https://coveralls.io/repos/Invoiced/invoiced-php/badge.svg?branch=master&service=github)](https://coveralls.io/github/Invoiced/invoiced-php?branch=master)

## Installing

The Invoiced package can be installed with composer:

```
composer install invoiced/invoiced
```

## Requirements

- >= PHP 5.4

## Usage

First, you must instantiate a new client

```php
$invoiced = new Invoiced\Client("{API_KEY}");
```

Then, API calls can be made like this:
```php
// retrieve invoice
$invoice = $invoiced->Invoice->retrieve("{INVOICE_ID}");

// mark as paid
$transaction = $invoiced->Transaction->create([
    'invoice' => $invoice->id,
    'amount' => $invoice->balance,
    'method' => "check"
]);
```

## Developing

The test suite can be ran with `phpunit`