<?php

use Invoiced\Client;
use Invoiced\Invoice;

class InvoiceTest extends PHPUnit_Framework_TestCase
{
    public static $invoiced;

    public static function setUpBeforeClass()
    {
        self::$invoiced = new Client('test');
    }

    public function testCreate()
    {
        $invoice = self::$invoiced->Invoice->create(['customer' => 123]);
    }

    public function testRetrieve()
    {
        $invoice = self::$invoiced->Invoice->retrieve('test');
    }

    public function testUpdate()
    {
        $invoice = new Invoice(self::$invoiced, 'test');
        $invoice->closed = true;
        $this->assertTrue($invoice->save());
    }

    public function testAll()
    {
        list($invoices, $metadata) = self::$invoiced->Invoice->all();
    }

    public function testDelete()
    {
        $invoice = new Invoice(self::$invoiced, 'test');
        $this->assertTrue($invoice->delete());
    }

    public function testSend()
    {
        $invoice = new Invoice(self::$invoiced, 'test');
        $emails = $invoice->send();
    }

    public function testPay()
    {
        $invoice = new Invoice(self::$invoiced, 'test');
        $this->assertTrue($invoice->pay());
    }
}
