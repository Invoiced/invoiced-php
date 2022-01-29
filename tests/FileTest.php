<?php

namespace Invoiced\Tests;

use Invoiced\Tests\Traits\CreateTrait;
use Invoiced\Tests\Traits\DeleteTrait;
use Invoiced\Tests\Traits\GetEndpointTrait;
use Invoiced\Tests\Traits\RetrieveTrait;
use PHPUnit_Framework_TestCase;

class FileTest extends AbstractEndpointTestCase
{
    use GetEndpointTrait;
    use CreateTrait;
    use RetrieveTrait;
    use DeleteTrait;

    const OBJECT_CLASS = 'Invoiced\\File';
    const EXPECTED_ENDPOINT = '/files/123';
}
