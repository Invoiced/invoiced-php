<?php

namespace Invoiced\Tests;

use Invoiced\Tests\Traits\GetEndpointTrait;
use Invoiced\Tests\Traits\ListTrait;

class InboxTest extends AbstractEndpointTestCase
{
    use GetEndpointTrait;
    use ListTrait;

    const OBJECT_CLASS = 'Invoiced\\Inbox';
    const EXPECTED_ENDPOINT = '/inboxes/123';
}
