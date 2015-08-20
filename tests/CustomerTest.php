<?php

use Invoiced\Client;
use Invoiced\Customer;

class CustomerTest extends PHPUnit_Framework_TestCase
{
	static $invoiced;

	static function setUpBeforeClass()
	{
		self::$invoiced = new Client('test');
	}

	function testCreate()
	{
		$customer = self::$invoiced->Customer->create(['name' => 'Pied Piper']);
	}

	function testRetrieve()
	{
		$customer = self::$invoiced->Customer->retrieve('test');
	}

	function testUpdate()
	{
		$customer = new Customer(self::$invoiced, 'test');
		$customer->notes = 'Terrible customer';
		$this->assertTrue($customer->save());
	}

	function testAll()
	{
		list($customers, $metadata) = self::$invoiced->Customer->all(); 
	}

	function testDelete()
	{
		$customer = new Customer(self::$invoiced, 'test');
		$this->assertTrue($customer->delete());
	}

	function testSendStatement()
	{
		$customer = new Customer(self::$invoiced, 'test');
		$emails = $customer->sendStatement();
	}

	function testBalance()
	{
		$customer = new Customer(self::$invoiced, 'test');

		$expected = new stdClass;
		$expected->past_due = false;
		$expected->available_credits = 0;
		$expected->total_outstanding = 0;

		$this->assertEquals($expected, $customer->balance());
	}

	function testSubscriptions()
	{
		$customer = new Customer(self::$invoiced, 'test');
		list($subscriptions, $metadata) = $customer->subscriptions();
	}
}