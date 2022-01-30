<?php

namespace Invoiced\Tests;

use Invoiced\Tests\Traits\CreateTrait;
use Invoiced\Tests\Traits\DeleteTrait;
use Invoiced\Tests\Traits\GetEndpointTrait;
use Invoiced\Tests\Traits\ListTrait;
use Invoiced\Tests\Traits\RetrieveTrait;
use Invoiced\Tests\Traits\UpdateTrait;

class PaymentMethodTest extends AbstractEndpointTestCase
{
    use GetEndpointTrait;
    use RetrieveTrait;
    use UpdateTrait;
    use ListTrait;

    const OBJECT_CLASS = 'Invoiced\\PaymentMethod';
    const EXPECTED_ENDPOINT = '/payment_methods/123';
}
