<?php

namespace Invoiced\Tests;

use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\Psr7\Response;
use Invoiced\Client;
use Invoiced\Refund;
use PHPUnit_Framework_TestCase;

class RefundTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var Client
     */
    public static $invoiced;

    /**
     * @return void
     */
    public static function setUpBeforeClass()
    {
        $mock = new MockHandler([
            new Response(201, [], '{"id":123,"amount":50,"object":"refund"}'),
        ]);

        self::$invoiced = new Client('API_KEY', false, null, $mock);
    }

    /**
     * @return void
     */
    public function testGetEndpoint()
    {
        $refund = new Refund(self::$invoiced, 123);
        $this->assertEquals('/123', $refund->getEndpoint());
    }

    /**
     * @return void
     */
    public function testCreate()
    {
        $refund = self::$invoiced->Refund->create(456, ['amount' => 50]);

        $this->assertInstanceOf('Invoiced\\Refund', $refund);
        $this->assertEquals(123, $refund->id);
        $this->assertEquals(50, $refund->amount);
    }
}
