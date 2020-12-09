<?php

namespace Invoiced\Tests;

use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\Psr7\Response;
use Invoiced\Client;
use Invoiced\CreditBalanceAdjustment;
use PHPUnit_Framework_TestCase;

class CreditBalanceAdjustmentTest extends PHPUnit_Framework_TestCase
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
            new Response(201, [], '{"id":"test","amount":800}'),
            new Response(200, [], '{"id":"test","amount":800}'),
            new Response(200, [], '{"id":"test","amount":800}'),
            new Response(401),
            new Response(200, ['X-Total-Count' => 15, 'Link' => '<https://api.invoiced.com/credit_balance_adjustments?per_page=25&page=1>; rel="self", <https://api.invoiced.com/credit_balance_adjustments?per_page=25&page=1>; rel="first", <https://api.invoiced.com/credit_balance_adjustments?per_page=25&page=1>; rel="last"'], '[{"id":1234,"amount":800}]'),
            new Response(204),
        ]);

        self::$invoiced = new Client('API_KEY', false, null, $mock);
    }

    /**
     * @return void
     */
    public function testGetEndpoint()
    {
        $creditBalanceAdjustment = new CreditBalanceAdjustment(self::$invoiced, 'test');
        $this->assertEquals('/credit_balance_adjustments/test', $creditBalanceAdjustment->getEndpoint());
    }

    /**
     * @return void
     */
    public function testCreate()
    {
        $creditBalanceAdjustment = self::$invoiced->CreditBalanceAdjustment->create(['id' => 'test', 'amount' => 800]);

        $this->assertInstanceOf('Invoiced\\CreditBalanceAdjustment', $creditBalanceAdjustment);
        $this->assertEquals('test', $creditBalanceAdjustment->id);
        $this->assertEquals(800, $creditBalanceAdjustment->amount);
    }

    /**
     * @return void
     */
    public function testRetrieveNoId()
    {
        $this->setExpectedException('InvalidArgumentException');
        self::$invoiced->CreditBalanceAdjustment->retrieve('');
    }

    /**
     * @return void
     */
    public function testRetrieve()
    {
        $creditBalanceAdjustment = self::$invoiced->CreditBalanceAdjustment->retrieve('test');
    }

    /**
     * @return void
     */
    public function testUpdateNoValue()
    {
        $creditBalanceAdjustment = new CreditBalanceAdjustment(self::$invoiced, 'test');
        $this->assertFalse($creditBalanceAdjustment->save());
    }

    /**
     * @return void
     */
    public function testUpdate()
    {
        $creditBalanceAdjustment = new CreditBalanceAdjustment(self::$invoiced, 'test');
        $creditBalanceAdjustment->amount = 800;
        $this->assertTrue($creditBalanceAdjustment->save());
    }

    /**
     * @return void
     */
    public function testUpdateFail()
    {
        $this->setExpectedException('Invoiced\\Error\\ApiError');

        $creditBalanceAdjustment = new CreditBalanceAdjustment(self::$invoiced, 'test');
        $creditBalanceAdjustment->amount = 800;
        $creditBalanceAdjustment->save();
    }

    /**
     * @return void
     */
    public function testAll()
    {
        list($creditBalanceAdjustments, $metadata) = self::$invoiced->CreditBalanceAdjustment->all();

        $this->assertTrue(is_array($creditBalanceAdjustments));
        $this->assertCount(1, $creditBalanceAdjustments);
        $this->assertEquals(1234, $creditBalanceAdjustments[0]->id);

        $this->assertInstanceOf('Invoiced\\Collection', $metadata);
        $this->assertEquals(15, $metadata->total_count);
    }

    /**
     * @return void
     */
    public function testDelete()
    {
        $creditBalanceAdjustment = new CreditBalanceAdjustment(self::$invoiced, 'test');
        $this->assertTrue($creditBalanceAdjustment->delete());
    }
}
