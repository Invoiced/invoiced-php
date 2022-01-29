<?php

namespace Invoiced\Tests;

use GuzzleHttp\Psr7\Response;
use Invoiced\PaymentSource;
use Invoiced\Tests\Traits\GetEndpointTrait;

class PaymentSourceTest extends AbstractEndpointTestCase
{
    use GetEndpointTrait;

    const OBJECT_CLASS = 'Invoiced\\PaymentSource';
    const EXPECTED_ENDPOINT = '/payment_sources/123';

    /**
     * @return void
     */
    public function testAll()
    {
        $client = $this->makeClient(new Response(200, ['X-Total-Count' => 15, 'Link' => '<https://api.invoiced.com/customers/12/payment_sources?per_page=25&page=1>; rel="self", <https://api.invoiced.com/customers/12/payment_sources?per_page=25&page=1>; rel="first", <https://api.invoiced.com/customers/12/payment_sources?per_page=25&page=1>; rel="last"'], '[{"id":1231,"object":"card"}, {"id": 2342,"object":"bank_account"}]'));
        $source = new PaymentSource($client);
        list($sources, $metadata) = $source->all();

        $this->assertTrue(is_array($sources));
        $this->assertCount(2, $sources);
        $this->assertEquals(1231, $sources[0]->id);
        $this->assertInstanceOf('Invoiced\\Card', $sources[0]);
        $this->assertInstanceOf('Invoiced\\BankAccount', $sources[1]);

        $this->assertInstanceOf('Invoiced\\Collection', $metadata);
        $this->assertEquals(15, $metadata->total_count);
    }
}
