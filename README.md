invoiced-php
========

This repository contains the PHP client library for the [Invoiced](https://invoiced.com) API.

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
$invoice = $invoiced->Invoice::retrieve("{INVOICE_ID}");

// mark as paid
$transaction = $invoiced->Transaction::create([
    'invoice' => $invoice->id,
    'amount' => $invoice->balance,
    'method' => "check"
]);
```

## Developing

The test suite can be ran with `phpunit`