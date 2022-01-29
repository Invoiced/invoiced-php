<?php

namespace Invoiced\Tests;

use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\Psr7\Response;
use Invoiced\Client;
use Invoiced\Refund;
use Invoiced\Tests\Traits\GetEndpointTrait;
use PHPUnit_Framework_TestCase;

class RefundTest extends AbstractEndpointTestCase
{
    use GetEndpointTrait;

    const OBJECT_CLASS = 'Invoiced\\Refund';
    const EXPECTED_ENDPOINT = '/123';

    /**
     * @return void
     */
    public function testCreate()
    {
        $client = $this->makeClient(new Response(201, [], '{"id":123,"amount":50,"object":"refund"}'));
        $refund = (new Refund($client))->create(456, ['amount' => 50]);

        $this->assertInstanceOf('Invoiced\\Refund', $refund);
        $this->assertEquals(123, $refund->id);
        $this->assertEquals(50, $refund->amount);
    }
}
