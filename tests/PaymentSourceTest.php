<?php

use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\Psr7\Response;
use Invoiced\Client;
use Invoiced\PaymentSource;

class PaymentSourceTest extends PHPUnit_Framework_TestCase
{
    public static $invoiced;

    public static function setUpBeforeClass()
    {
        $mock = new MockHandler([
            new Response(200, ['X-Total-Count' => 15, 'Link' => '<https://api.invoiced.com/customers/12/payment_sources?per_page=25&page=1>; rel="self", <https://api.invoiced.com/customers/12/payment_sources?per_page=25&page=1>; rel="first", <https://api.invoiced.com/customers/12/payment_sources?per_page=25&page=1>; rel="last"'], '[{"id":1231,"object":"card"}, {"id": 2342,"object":"bank_account"}]'),
        ]);

        self::$invoiced = new Client('API_KEY', false, false, $mock);
    }

    public function testGetEndpoint()
    {
        $plan = new PaymentSource(self::$invoiced, 123);
        $this->assertEquals('/payment_sources/123', $plan->getEndpoint());
    }

    public function testAll()
    {
        $source = new PaymentSource(self::$invoiced);
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
