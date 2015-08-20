<?php

use Invoiced\Client;
use Invoiced\Invoice;

class InvoiceTest extends PHPUnit_Framework_TestCase
{
	static $invoiced;

	static function setUpBeforeClass()
	{
		self::$invoiced = new Client('test');
	}

	function testCreate()
	{
		$invoice = self::$invoiced->Invoice->create(['customer' => 123]);
	}

	function testRetrieve()
	{
		$invoice = self::$invoiced->Invoice->retrieve('test');
	}

	function testUpdate()
	{
		$invoice = new Invoice(self::$invoiced, 'test');
		$invoice->closed = true;
		$this->assertTrue($invoice->save());
	}

	function testAll()
	{
		list($invoices, $metadata) = self::$invoiced->Invoice->all(); 
	}

	function testDelete()
	{
		$invoice = new Invoice(self::$invoiced, 'test');
		$this->assertTrue($invoice->delete());
	}

	function testSend()
	{
		$invoice = new Invoice(self::$invoiced, 'test');
		$emails = $invoice->send();
	}

	function testPay()
	{
		$invoice = new Invoice(self::$invoiced, 'test');
		$this->assertTrue($invoice->pay());
	}
}