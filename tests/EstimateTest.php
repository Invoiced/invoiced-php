<?php

namespace Invoiced\Tests;

use GuzzleHttp\Psr7\Response;
use Invoiced\Estimate;
use Invoiced\Tests\Traits\AttachmentsTrait;
use Invoiced\Tests\Traits\CreateTrait;
use Invoiced\Tests\Traits\DeleteTrait;
use Invoiced\Tests\Traits\GetEndpointTrait;
use Invoiced\Tests\Traits\ListTrait;
use Invoiced\Tests\Traits\RetrieveTrait;
use Invoiced\Tests\Traits\SendTrait;
use Invoiced\Tests\Traits\UpdateTrait;
use Invoiced\Tests\Traits\VoidTrait;

class EstimateTest extends AbstractEndpointTestCase
{
    use GetEndpointTrait;
    use CreateTrait;
    use RetrieveTrait;
    use UpdateTrait;
    use DeleteTrait;
    use ListTrait;
    use VoidTrait;
    use SendTrait;
    use AttachmentsTrait;

    const OBJECT_CLASS = 'Invoiced\\Estimate';
    const EXPECTED_ENDPOINT = '/estimates/123';

    /**
     * @return void
     */
    public function testInvoice()
    {
        $client = $this->makeClient(new Response(201, [], '{"id":456,"total":500}'));
        $estimate = new Estimate($client, 456);
        $invoice = $estimate->invoice();

        $this->assertInstanceOf('Invoiced\\Invoice', $invoice);
        $this->assertEquals(456, $invoice->id);
        $this->assertEquals(500, $invoice->total);
    }
}
