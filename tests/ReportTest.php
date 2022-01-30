<?php

namespace Invoiced\Tests;

use Invoiced\Tests\Traits\CreateTrait;
use Invoiced\Tests\Traits\GetEndpointTrait;
use Invoiced\Tests\Traits\RetrieveTrait;

class ReportTest extends AbstractEndpointTestCase
{
    use GetEndpointTrait;
    use CreateTrait;
    use RetrieveTrait;

    const OBJECT_CLASS = 'Invoiced\\Report';
    const EXPECTED_ENDPOINT = '/reports/123';
}
