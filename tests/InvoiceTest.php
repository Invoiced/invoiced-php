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
            new Response(200, ['X-Total-Count' => 10, 'Link' => '<https://api.invoiced.com/invoices/123/attachments?per_page=25&page=1>; rel="self", <https://api.invoiced.com/invoices/123/attachments?per_page=25&page=1>; rel="first", <https://api.invoiced.com/invoices/123/attachments?per_page=25&page=1>; rel="last"'], '[{"file":{"id":456}}]'),
            new Response(201, [], '{"id":123,"status":"active"}'),
            new Response(200, [], '{"id":"123","status":"active"}'),
            new Response(201, [], '[{"id":5678,"message":"test"}]'),
            new Response(201, [], '[{"id":6789,"state":"queued"}]'),
            new Response(201, [], '{"id":1212,"notes":"test"}'),
            new Response(200, [], '{"id":"1212","notes":"test"}'),
            new Response(200, ['X-Total-Count' => 15, 'Link' => '<https://api.invoiced.com/invoices/456/notes?per_page=25&page=1>; rel="self", <https://api.invoiced.com/invoices/456/notes?per_page=25&page=1>; rel="first", <https://api.invoiced.com/invoices/456/notes?per_page=25&page=1>; rel="last"'], '[{"id":1212,"notes":"test"}]'),
            new Response(200, [], '{"id":"1234","status":"voided"}'),
        ]);

        self::$invoiced = new Client('API_KEY', false, false, $mock);
    }

    public function testGetEndpoint()
    {
        $invoice = new Invoice(self::$invoiced, 123);
        $this->assertEquals('/invoices/123', $invoice->getEndpoint());
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

    public function testAttachments()
    {
        $invoice = new Invoice(self::$invoiced, 123);
        list($attachments, $metadata) = $invoice->attachments();
        $this->assertTrue(is_array($attachments));
        $this->assertCount(1, $attachments);
        $this->assertEquals(456, $attachments[0]->id);
        $this->assertInstanceOf('Invoiced\\Collection', $metadata);
        $this->assertEquals(10, $metadata->total_count);
    }

    public function testCreatePaymentPlan()
    {
        $invoice = new Invoice(self::$invoiced, 456);
        $paymentPlan = $invoice->paymentPlan()->create(['date' => 1234, 'amount' => 100]);

        $this->assertInstanceOf('Invoiced\\PaymentPlan', $paymentPlan);
        $this->assertEquals(123, $paymentPlan->id);
        $this->assertEquals('active', $paymentPlan->status);
        $this->assertEquals('/invoices/456/payment_plan', $paymentPlan->getEndpoint());
    }

    public function testRetrievePaymentPlan()
    {
        $invoice = new Invoice(self::$invoiced, 456);
        $paymentPlan = $invoice->paymentPlan()->get();

        $this->assertInstanceOf('Invoiced\\PaymentPlan', $paymentPlan);
        $this->assertEquals(123, $paymentPlan->id);
        $this->assertEquals('active', $paymentPlan->status);
        $this->assertEquals('/invoices/456/payment_plan', $paymentPlan->getEndpoint());
    }

    public function testSendSMS()
    {
        $invoice = new Invoice(self::$invoiced, 123);
        $textMessages = $invoice->sendSMS();

        $this->assertTrue(is_array($textMessages));
        $this->assertCount(1, $textMessages);
        $this->assertInstanceOf('Invoiced\\TextMessage', $textMessages[0]);
        $this->assertEquals(5678, $textMessages[0]->id);
    }

    public function testSendLetter()
    {
        $invoice = new Invoice(self::$invoiced, 123);
        $letters = $invoice->sendLetter();

        $this->assertTrue(is_array($letters));
        $this->assertCount(1, $letters);
        $this->assertInstanceOf('Invoiced\\Letter', $letters[0]);
        $this->assertEquals(6789, $letters[0]->id);
    }

    public function testCreateNote()
    {
        $invoice = new Invoice(self::$invoiced, 456);
        $note = $invoice->notes()->create(['notes' => 'test']);

        $this->assertInstanceOf('Invoiced\\Note', $note);
        $this->assertEquals(1212, $note->id);
        $this->assertEquals('test', $note->notes);
        $this->assertEquals('/invoices/456/notes/1212', $note->getEndpoint());
    }

    public function testRetrieveNote()
    {
        $invoice = new Invoice(self::$invoiced, 456);
        $note = $invoice->notes()->retrieve(1212);

        $this->assertInstanceOf('Invoiced\\Note', $note);
        $this->assertEquals(1212, $note->id);
        $this->assertEquals('test', $note->notes);
        $this->assertEquals('/invoices/456/notes/1212', $note->getEndpoint());
    }

    public function testAllNotes()
    {
        $invoice = new Invoice(self::$invoiced, 456);
        list($notes, $metadata) = $invoice->notes()->all();

        $this->assertTrue(is_array($notes));
        $this->assertCount(1, $notes);
        $this->assertEquals(1212, $notes[0]->id);
        $this->assertEquals('/invoices/456/notes/1212', $notes[0]->getEndpoint());

        $this->assertInstanceOf('Invoiced\\Collection', $metadata);
        $this->assertEquals(15, $metadata->total_count);
    }

    public function testVoid()
    {
        $invoice = new Invoice(self::$invoiced, 1234);
        $invoice->void();

        $this->assertInstanceOf('Invoiced\\Invoice', $invoice);
        $this->assertEquals(1234, $invoice->id);
        $this->assertEquals('voided', $invoice->status);
    }
}
