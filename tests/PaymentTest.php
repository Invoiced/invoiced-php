<?php

namespace Invoiced\Tests;

use Invoiced\Tests\Traits\CreateTrait;
use Invoiced\Tests\Traits\DeleteTrait;
use Invoiced\Tests\Traits\GetEndpointTrait;
use Invoiced\Tests\Traits\ListTrait;
use Invoiced\Tests\Traits\RetrieveTrait;
use Invoiced\Tests\Traits\SendTrait;
use Invoiced\Tests\Traits\UpdateTrait;

class PaymentTest extends AbstractEndpointTestCase
{
    use GetEndpointTrait;
    use CreateTrait;
    use RetrieveTrait;
    use UpdateTrait;
    use DeleteTrait;
    use ListTrait;
    use SendTrait;

    const OBJECT_CLASS = 'Invoiced\\Payment';
    const EXPECTED_ENDPOINT = '/payments/123';
}
