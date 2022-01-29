<?php

namespace Invoiced\Tests;

use Invoiced\Tests\Traits\GetEndpointTrait;
use Invoiced\Tests\Traits\ListTrait;

class EventTest extends AbstractEndpointTestCase
{
    use ListTrait;
    use GetEndpointTrait;

    const OBJECT_CLASS = 'Invoiced\\Event';
    const EXPECTED_ENDPOINT = '/events/123';
}
