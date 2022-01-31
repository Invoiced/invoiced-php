invoiced-php
========

This repository contains the PHP client library for the [Invoiced](https://invoiced.com) API.

[![CI](https://github.com/Invoiced/invoiced-php/actions/workflows/ci.yml/badge.svg)](https://github.com/Invoiced/invoiced-php/actions/workflows/ci.yml)
[![Coverage Status](https://coveralls.io/repos/Invoiced/invoiced-php/badge.svg?branch=master&service=github)](https://coveralls.io/github/Invoiced/invoiced-php?branch=master)
[![PHP version](https://badge.fury.io/ph/invoiced%2Finvoiced.svg)](https://badge.fury.io/ph/invoiced%2Finvoiced)

## Installing

The Invoiced package can be installed with composer:

```
composer require invoiced/invoiced
```

## Requirements

- PHP 5.5+, PHP 7+, or PHP 8+
- [Composer](https://getcomposer.org/)

## Usage

First, you must instantiate a new client

```php
$invoiced = new Invoiced\Client('{API_KEY}');
```

Then, API calls can be made like this:
```php
// retrieve invoice
$invoice = $invoiced->Invoice->retrieve('{INVOICE_ID}');

// mark as paid
$payment = $invoiced->Payment->create([
    'amount' => $invoice->balance,
    'method' => 'check',
    'applied_to' => [
        [
            'type' => 'invoice',
            'invoice' => $invoice->id,
            'amount' => $invoice->balance,
        ],
    ]
]);
```

If you want to use the sandbox API instead then you must set the second argument on the client to `true` like this:

```php
$invoiced = new Invoiced\Client("{SANDBOX_API_KEY}", true);
```

## Developing

The test suite can be ran with `phpunit`

## Deploying

In order to deploy a new version to Packagist, a new release must be created in GitHub.