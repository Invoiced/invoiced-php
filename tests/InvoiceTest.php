<?php

use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\Psr7\Response;
use Invoiced\Client;
use Invoiced\Invoice;

class InvoiceTest extends PHPUnit_Framework_TestCase
{
    public static $invoiced;

    public static function setUpBeforeClass()
    {
        $mock = new MockHandler([
            new Response(201, [], '{"id":123,"number":"INV-0001"}'),
            new Response(200, [], '{"id":123,"number":"INV-0001"}'),
            new Response(200, [], '{"id":123,"closed":true}'),
            new Response(401),
            new Response(200, ['X-Total-Count' => 15, 'Link' => '<https://api.invoiced.com/invoices?per_page=25&page=1>; rel="self", <https://api.invoiced.com/invoices?per_page=25&page=1>; rel="first", <https://api.invoiced.com/invoices?per_page=25&page=1>; rel="last"'], '[{"id":123,"number":"INV-0001"}]'),
            new Response(204),
            new Response(201, [], '[{"id":4567,"email":"test@example.com"}]'),
            new Response(200, [], '{"paid":true}'),
        ]);

        self::$invoiced = new Client('API_KEY', $mock);
    }

    public function testCreate()
    {
        $invoice = self::$invoiced->Invoice->create(['customer' => 123]);

        $this->assertInstanceOf('Invoiced\\Invoice', $invoice);
        $this->assertEquals(123, $invoice->id);
        $this->assertEquals('INV-0001', $invoice->number);
    }

    public function testRetrieveNoId()
    {
        $this->setExpectedException('InvalidArgumentException');
        self::$invoiced->Invoice->retrieve(false);
    }

    public function testRetrieve()
    {
        $invoice = self::$invoiced->Invoice->retrieve(123);

        $this->assertInstanceOf('Invoiced\\Invoice', $invoice);
        $this->assertEquals(123, $invoice->id);
        $this->assertEquals('INV-0001', $invoice->number);
    }

    public function testUpdateNoValue()
    {
        $invoice = new Invoice(self::$invoiced, 123);
        $this->assertFalse($invoice->save());
    }

    public function testUpdate()
    {
        $invoice = new Invoice(self::$invoiced, 123);
        $invoice->closed = true;
        $this->assertTrue($invoice->save());

        $this->assertTrue($invoice->closed);
    }

    public function testUpdateFail()
    {
        $this->setExpectedException('Invoiced\\Error\\ApiError');

        $invoice = new Invoice(self::$invoiced, 123);
        $invoice->closed = true;
        $invoice->save();
    }

    public function testAll()
    {
        list($invoices, $metadata) = self::$invoiced->Invoice->all();

        $this->assertTrue(is_array($invoices));
        $this->assertCount(1, $invoices);
        $this->assertEquals(123, $invoices[0]->id);

        $this->assertInstanceOf('Invoiced\\Collection', $metadata);
        $this->assertEquals(15, $metadata->total_count);
    }

    public function testDelete()
    {
        $invoice = new Invoice(self::$invoiced, 123);
        $this->assertTrue($invoice->delete());
    }

    public function testSend()
    {
        $invoice = new Invoice(self::$invoiced, 123);
        $emails = $invoice->send();

        $this->assertTrue(is_array($emails));
        $this->assertCount(1, $emails);
        $this->assertInstanceOf('Invoiced\\Email', $emails[0]);
        $this->assertEquals(4567, $emails[0]->id);
    }

    public function testPay()
    {
        $invoice = new Invoice(self::$invoiced, 123);
        $this->assertTrue($invoice->pay());
        $this->assertTrue($invoice->paid);
    }
}
