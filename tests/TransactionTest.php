<?php

use Invoiced\Client;
use Invoiced\Transaction;

class TransactionTest extends PHPUnit_Framework_TestCase
{
	static $invoiced;

	static function setUpBeforeClass()
	{
		self::$invoiced = new Client('test');
	}

	function testCreate()
	{
		$transaction = self::$invoiced->Transaction->create(['customer' => 123]);
	}

	function testRetrieve()
	{
		$transaction = self::$invoiced->Transaction->retrieve('test');
	}

	function testUpdate()
	{
		$transaction = new Transaction(self::$invoiced, 'test');
		$transaction->closed = true;
		$this->assertTrue($transaction->save());
	}

	function testAll()
	{
		list($transactions, $metadata) = self::$invoiced->Transaction->all(); 
	}

	function testDelete()
	{
		$transaction = new Transaction(self::$invoiced, 'test');
		$this->assertTrue($transaction->delete());
	}

	function testSend()
	{
		$transaction = new Transaction(self::$invoiced, 'test');
		$emails = $transaction->send();
	}

	function testRefund()
	{
		$transaction = new Transaction(self::$invoiced, 'test');
		$this->assertTrue($transaction->refund(['amount' => 100]));
	}
}