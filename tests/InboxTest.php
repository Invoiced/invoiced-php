<?php

namespace Invoiced\Tests;

use Invoiced\Tests\Traits\CreateTrait;
use Invoiced\Tests\Traits\DeleteTrait;
use Invoiced\Tests\Traits\GetEndpointTrait;
use Invoiced\Tests\Traits\ListTrait;
use Invoiced\Tests\Traits\RetrieveTrait;
use Invoiced\Tests\Traits\UpdateTrait;

class InboxTest extends AbstractEndpointTestCase
{
    use GetEndpointTrait;
    use ListTrait;

    const OBJECT_CLASS = 'Invoiced\\Inbox';
    const EXPECTED_ENDPOINT = '/inboxes/123';
}