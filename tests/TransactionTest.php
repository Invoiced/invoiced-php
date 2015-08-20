<?php

use Invoiced\Client;
use Invoiced\Transaction;

class TransactionTest extends PHPUnit_Framework_TestCase
{
    public static $invoiced;

    public static function setUpBeforeClass()
    {
        self::$invoiced = new Client('test');
    }

    public function testCreate()
    {
        $transaction = self::$invoiced->Transaction->create(['customer' => 123]);
    }

    public function testRetrieve()
    {
        $transaction = self::$invoiced->Transaction->retrieve('test');
    }

    public function testUpdate()
    {
        $transaction = new Transaction(self::$invoiced, 'test');
        $transaction->closed = true;
        $this->assertTrue($transaction->save());
    }

    public function testAll()
    {
        list($transactions, $metadata) = self::$invoiced->Transaction->all();
    }

    public function testDelete()
    {
        $transaction = new Transaction(self::$invoiced, 'test');
        $this->assertTrue($transaction->delete());
    }

    public function testSend()
    {
        $transaction = new Transaction(self::$invoiced, 'test');
        $emails = $transaction->send();
    }

    public function testRefund()
    {
        $transaction = new Transaction(self::$invoiced, 'test');
        $this->assertTrue($transaction->refund(['amount' => 100]));
    }
}
