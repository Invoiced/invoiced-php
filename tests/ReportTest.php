<?php

namespace Invoiced\Tests;

use Invoiced\Tests\Traits\CreateTrait;
use Invoiced\Tests\Traits\DeleteTrait;
use Invoiced\Tests\Traits\GetEndpointTrait;
use Invoiced\Tests\Traits\ListTrait;
use Invoiced\Tests\Traits\RetrieveTrait;
use Invoiced\Tests\Traits\UpdateTrait;

class ReportTest extends AbstractEndpointTestCase
{
    use GetEndpointTrait;
    use CreateTrait;
    use RetrieveTrait;

    const OBJECT_CLASS = 'Invoiced\\Report';
    const EXPECTED_ENDPOINT = '/reports/123';
}