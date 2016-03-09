<?php

use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\Psr7\Response;
use Invoiced\Client;
use Invoiced\Transaction;

class TransactionTest extends PHPUnit_Framework_TestCase
{
    public static $invoiced;

    public static function setUpBeforeClass()
    {
        $mock = new MockHandler([
            new Response(201, [], '{"id":123,"amount":100}'),
            new Response(200, [], '{"id":123,"amount":100}'),
            new Response(200, [], '{"id":123,"sent":true}'),
            new Response(401),
            new Response(200, ['X-Total-Count' => 15, 'Link' => '<https://api.invoiced.com/transactions?per_page=25&page=1>; rel="self", <https://api.invoiced.com/transactions?per_page=25&page=1>; rel="first", <https://api.invoiced.com/transactions?per_page=25&page=1>; rel="last"'], '[{"id":123,"amount":100}]'),
            new Response(204),
            new Response(201, [], '[{"id":4567,"email":"test@example.com"}]'),
            new Response(200, [], '{"id":456}'),
        ]);

        self::$invoiced = new Client('API_KEY', false, $mock);
    }

    public function testGetEndpoint()
    {
        $transaction = new Transaction(self::$invoiced, 123);
        $this->assertEquals('/transactions/123', $transaction->getEndpoint());
    }

    public function testCreate()
    {
        $transaction = self::$invoiced->Transaction->create(['customer' => 123]);

        $this->assertInstanceOf('Invoiced\\Transaction', $transaction);
        $this->assertEquals(123, $transaction->id);
        $this->assertEquals(100, $transaction->amount);
    }

    public function testRetrieveNoId()
    {
        $this->setExpectedException('InvalidArgumentException');
        self::$invoiced->Transaction->retrieve(false);
    }

    public function testRetrieve()
    {
        $transaction = self::$invoiced->Transaction->retrieve(123);
    }

    public function testUpdateNoValue()
    {
        $transaction = new Transaction(self::$invoiced, 123);
        $this->assertFalse($transaction->save());
    }

    public function testUpdate()
    {
        $transaction = new Transaction(self::$invoiced, 123);
        $transaction->closed = true;
        $this->assertTrue($transaction->save());
    }

    public function testUpdateFail()
    {
        $this->setExpectedException('Invoiced\\Error\\ApiError');

        $transaction = new Transaction(self::$invoiced, 123);
        $transaction->sent = true;
        $transaction->save();
    }

    public function testAll()
    {
        list($transactions, $metadata) = self::$invoiced->Transaction->all();

        $this->assertTrue(is_array($transactions));
        $this->assertCount(1, $transactions);
        $this->assertEquals(123, $transactions[0]->id);

        $this->assertInstanceOf('Invoiced\\Collection', $metadata);
        $this->assertEquals(15, $metadata->total_count);
    }

    public function testDelete()
    {
        $transaction = new Transaction(self::$invoiced, 123);
        $this->assertTrue($transaction->delete());
    }

    public function testSend()
    {
        $transaction = new Transaction(self::$invoiced, 123);
        $emails = $transaction->send();

        $this->assertTrue(is_array($emails));
        $this->assertCount(1, $emails);
        $this->assertInstanceOf('Invoiced\\Email', $emails[0]);
        $this->assertEquals(4567, $emails[0]->id);
    }

    public function testRefund()
    {
        $transaction = new Transaction(self::$invoiced, 123);
        $refund = $transaction->refund(['amount' => 100]);

        $this->assertInstanceOf('Invoiced\\Transaction', $refund);
        $this->assertEquals(456, $refund->id);
    }
}
