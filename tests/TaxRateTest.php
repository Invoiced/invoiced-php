<?php

namespace Invoiced\Tests;

use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\Psr7\Response;
use Invoiced\Client;
use Invoiced\TaxRate;
use PHPUnit_Framework_TestCase;

class TaxRateTest extends PHPUnit_Framework_TestCase
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
            new Response(201, [], '{"id":"test","name":"Test"}'),
            new Response(200, [], '{"id":"test","name":"Test"}'),
            new Response(200, [], '{"id":"test","name":"Some Tax"}'),
            new Response(401),
            new Response(200, ['X-Total-Count' => 15, 'Link' => '<https://api.invoiced.com/tax_rates?per_page=25&page=1>; rel="self", <https://api.invoiced.com/tax_rates?per_page=25&page=1>; rel="first", <https://api.invoiced.com/tax_rates?per_page=25&page=1>; rel="last"'], '[{"id":"test","name":"Some Item"}]'),
            new Response(204),
        ]);

        self::$invoiced = new Client('API_KEY', false, null, $mock);
    }

    /**
     * @return void
     */
    public function testGetEndpoint()
    {
        $taxRate = new TaxRate(self::$invoiced, 'test');
        $this->assertEquals('/tax_rates/test', $taxRate->getEndpoint());
    }

    /**
     * @return void
     */
    public function testCreate()
    {
        $taxRate = self::$invoiced->TaxRate->create(['id' => 'test', 'name' => 'Test']);

        $this->assertInstanceOf('Invoiced\\TaxRate', $taxRate);
        $this->assertEquals('test', $taxRate->id);
        $this->assertEquals('Test', $taxRate->name);
    }

    /**
     * @return void
     */
    public function testRetrieveNoId()
    {
        $this->setExpectedException('InvalidArgumentException');
        self::$invoiced->TaxRate->retrieve('');
    }

    /**
     * @return void
     */
    public function testRetrieve()
    {
        $taxRate = self::$invoiced->TaxRate->retrieve('test');
    }

    /**
     * @return void
     */
    public function testUpdateNoValue()
    {
        $taxRate = new TaxRate(self::$invoiced, 'test');
        $this->assertFalse($taxRate->save());
    }

    /**
     * @return void
     */
    public function testUpdate()
    {
        $taxRate = new TaxRate(self::$invoiced, 'test');
        $taxRate->name = 'Some Item';
        $this->assertTrue($taxRate->save());
    }

    /**
     * @return void
     */
    public function testUpdateFail()
    {
        $this->setExpectedException('Invoiced\\Error\\ApiError');

        $taxRate = new TaxRate(self::$invoiced, 'test');
        $taxRate->name = 'Test';
        $taxRate->save();
    }

    /**
     * @return void
     */
    public function testAll()
    {
        list($taxRates, $metadata) = self::$invoiced->TaxRate->all();

        $this->assertTrue(is_array($taxRates));
        $this->assertCount(1, $taxRates);
        $this->assertEquals('test', $taxRates[0]->id);

        $this->assertInstanceOf('Invoiced\\Collection', $metadata);
        $this->assertEquals(15, $metadata->total_count);
    }

    /**
     * @return void
     */
    public function testDelete()
    {
        $taxRate = new TaxRate(self::$invoiced, 'test');
        $this->assertTrue($taxRate->delete());
    }
}
