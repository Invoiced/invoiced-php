<?php

namespace Invoiced\Tests;

use Invoiced\Tests\Traits\CreateTrait;
use Invoiced\Tests\Traits\DeleteTrait;
use Invoiced\Tests\Traits\GetEndpointTrait;
use Invoiced\Tests\Traits\ListTrait;
use Invoiced\Tests\Traits\RetrieveTrait;
use Invoiced\Tests\Traits\UpdateTrait;

class CreditBalanceAdjustmentTest extends AbstractEndpointTestCase
{
    use GetEndpointTrait;
    use CreateTrait;
    use RetrieveTrait;
    use UpdateTrait;
    use DeleteTrait;
    use ListTrait;

    const OBJECT_CLASS = 'Invoiced\\CreditBalanceAdjustment';
    const EXPECTED_ENDPOINT = '/credit_balance_adjustments/123';
}
