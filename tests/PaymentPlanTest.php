<?php

namespace Invoiced\Tests;

use GuzzleHttp\Psr7\Response;
use Invoiced\PaymentPlan;
use Invoiced\Tests\Traits\CreateTrait;
use Invoiced\Tests\Traits\DeleteTrait;
use Invoiced\Tests\Traits\GetEndpointTrait;

class PaymentPlanTest extends AbstractEndpointTestCase
{
    use GetEndpointTrait;
    use CreateTrait;
    use DeleteTrait;

    const OBJECT_CLASS = 'Invoiced\\PaymentPlan';
    const EXPECTED_ENDPOINT = '/payment_plan';

    /**
     * @return void
     */
    public function testRetrieve()
    {
        $this->expectException('BadMethodCallException');

        $plan = new PaymentPlan($this->makeClient(), null, []);
        $paymentPlan = $plan->retrieve(456);
    }

    /**
     * @return void
     */
    public function testGet()
    {
        $client = $this->makeClient(new Response(200, [], '{"id":456,"status":"active"}'));
        $plan = new PaymentPlan($client, null, []);
        $paymentPlan = $plan->get();

        $this->assertInstanceOf('Invoiced\\PaymentPlan', $paymentPlan);
        $this->assertEquals(456, $paymentPlan->id);
        $this->assertEquals('active', $paymentPlan->status);
    }
}
