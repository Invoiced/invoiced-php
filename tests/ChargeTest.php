<?php

namespace Invoiced\Tests;

use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\Psr7\Response;
use Invoiced\Client;
use Invoiced\Tests\Traits\GetEndpointTrait;
use PHPUnit_Framework_TestCase;

class ChargeTest extends AbstractEndpointTestCase
{
    use GetEndpointTrait;

    const OBJECT_CLASS = 'Invoiced\\Charge';
    const EXPECTED_ENDPOINT = '/charges/123';

    /**
     * @return void
     */
    public function testCreate()
    {
        $client = $this->makeClient(new Response(201, [], '{"id":123,"amount":100}'));
        $charge = $client->Charge->create(['customer' => 123]);

        $this->assertInstanceOf('Invoiced\\Payment', $charge);
        $this->assertEquals(123, $charge->id);
        $this->assertEquals(100, $charge->amount);
    }
}
