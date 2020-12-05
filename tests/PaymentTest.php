<?php

namespace Invoiced\Tests;

use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\Psr7\Response;
use Invoiced\Client;
use Invoiced\Payment;
use PHPUnit_Framework_TestCase;

class PaymentTest extends PHPUnit_Framework_TestCase
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
            new Response(201, [], '{"id":123,"amount":100}'),
            new Response(200, [], '{"id":123,"amount":100}'),
            new Response(200, [], '{"id":123,"status":"applied"}'),
            new Response(401),
            new Response(200, ['X-Total-Count' => 15, 'Link' => '<https://api.invoiced.com/payments?per_page=25&page=1>; rel="self", <https://api.invoiced.com/payments?per_page=25&page=1>; rel="first", <https://api.invoiced.com/payments?per_page=25&page=1>; rel="last"'], '[{"id":123,"amount":100}]'),
            new Response(204),
            new Response(201, [], '[{"id":4567,"email":"test@example.com"}]'),
        ]);

        self::$invoiced = new Client('API_KEY', false, null, $mock);
    }

    /**
     * @return void
     */
    public function testGetEndpoint()
    {
        $payment = new Payment(self::$invoiced, 123);
        $this->assertEquals('/payments/123', $payment->getEndpoint());
    }

    /**
     * @return void
     */
    public function testCreate()
    {
        $payment = self::$invoiced->Payment->create(['customer' => 123]);

        $this->assertInstanceOf('Invoiced\\Payment', $payment);
        $this->assertEquals(123, $payment->id);
        $this->assertEquals(100, $payment->amount);
    }

    /**
     * @return void
     */
    public function testRetrieveNoId()
    {
        $this->setExpectedException('InvalidArgumentException');
        self::$invoiced->Payment->retrieve('');
    }

    /**
     * @return void
     */
    public function testRetrieve()
    {
        $payment = self::$invoiced->Payment->retrieve(123);
    }

    /**
     * @return void
     */
    public function testUpdateNoValue()
    {
        $payment = new Payment(self::$invoiced, 123);
        $this->assertFalse($payment->save());
    }

    /**
     * @return void
     */
    public function testUpdate()
    {
        $payment = new Payment(self::$invoiced, 123);
        $payment->currency = 'usd';
        $this->assertTrue($payment->save());
    }

    /**
     * @return void
     */
    public function testUpdateFail()
    {
        $this->setExpectedException('Invoiced\\Error\\ApiError');

        $payment = new Payment(self::$invoiced, 123);
        $payment->status = 'failed';
        $payment->save();
    }

    /**
     * @return void
     */
    public function testAll()
    {
        list($payments, $metadata) = self::$invoiced->Payment->all();

        $this->assertTrue(is_array($payments));
        $this->assertCount(1, $payments);
        $this->assertEquals(123, $payments[0]->id);

        $this->assertInstanceOf('Invoiced\\Collection', $metadata);
        $this->assertEquals(15, $metadata->total_count);
    }

    /**
     * @return void
     */
    public function testDelete()
    {
        $payment = new Payment(self::$invoiced, 123);
        $this->assertTrue($payment->delete());
    }

    /**
     * @return void
     */
    public function testSend()
    {
        $payment = new Payment(self::$invoiced, 123);
        $emails = $payment->send();

        $this->assertTrue(is_array($emails));
        $this->assertCount(1, $emails);
        $this->assertInstanceOf('Invoiced\\Email', $emails[0]);
        $this->assertEquals(4567, $emails[0]->id);
    }
}
