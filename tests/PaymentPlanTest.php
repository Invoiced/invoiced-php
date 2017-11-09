<?php

use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\Psr7\Response;
use Invoiced\Client;
use Invoiced\PaymentPlan;

class PaymentPlanTest extends PHPUnit_Framework_TestCase
{
    public static $invoiced;

    public static function setUpBeforeClass()
    {
        $mock = new MockHandler([
            new Response(201, [], '{"id":456,"status":"active"}'),
            new Response(200, [], '{"id":456,"status":"active"}'),
            new Response(204),
        ]);

        self::$invoiced = new Client('API_KEY', false, false, $mock);
    }

    public function testGetEndpoint()
    {
        $plan = new PaymentPlan(self::$invoiced, 123);
        $this->assertEquals('/payment_plan', $plan->getEndpoint());
    }

    public function testCreate()
    {
        $plan = new PaymentPlan(self::$invoiced, null, []);
        $paymentPlan = $plan->create(['date' => 1234, 'amount' => 100]);

        $this->assertInstanceOf('Invoiced\\PaymentPlan', $paymentPlan);
        $this->assertEquals(456, $paymentPlan->id);
        $this->assertEquals('active', $paymentPlan->status);
    }

    public function testRetrieve()
    {
        $this->setExpectedException('BadMethodCallException');

        $plan = new PaymentPlan(self::$invoiced, null, []);
        $paymentPlan = $plan->retrieve(456);
    }

    public function testGet()
    {
        $plan = new PaymentPlan(self::$invoiced, null, []);
        $paymentPlan = $plan->get();

        $this->assertInstanceOf('Invoiced\\PaymentPlan', $paymentPlan);
        $this->assertEquals(456, $paymentPlan->id);
        $this->assertEquals('active', $paymentPlan->status);
    }

    public function testCancel()
    {
        $paymentPlan = new PaymentPlan(self::$invoiced, 456, []);
        $this->assertTrue($paymentPlan->cancel());
    }
}
