<?php

namespace Invoiced\Tests;

use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\Psr7\Response;
use Invoiced\Client;
use Invoiced\Invoice;
use Invoiced\Tests\Traits\AttachmentsTrait;
use Invoiced\Tests\Traits\CreateTrait;
use Invoiced\Tests\Traits\DeleteTrait;
use Invoiced\Tests\Traits\GetEndpointTrait;
use Invoiced\Tests\Traits\ListTrait;
use Invoiced\Tests\Traits\RetrieveTrait;
use Invoiced\Tests\Traits\SendTrait;
use Invoiced\Tests\Traits\UpdateTrait;
use Invoiced\Tests\Traits\VoidTrait;

class InvoiceTest extends AbstractEndpointTestCase
{
    use GetEndpointTrait;
    use CreateTrait;
    use RetrieveTrait;
    use UpdateTrait;
    use DeleteTrait;
    use ListTrait;
    use VoidTrait;
    use SendTrait;
    use AttachmentsTrait;

    const OBJECT_CLASS = 'Invoiced\\Invoice';
    const EXPECTED_ENDPOINT = '/invoices/123';

    /**
     * @var Client
     */
    public static $invoiced;

    /**
     * @return void
     */
    public static function set_up_before_class()
    {
        $mock = new MockHandler([
            new Response(200, [], '{"paid":true}'),
            new Response(201, [], '{"id":123,"status":"active"}'),
            new Response(200, [], '{"id":"123","status":"active"}'),
            new Response(201, [], '[{"id":5678,"message":"test"}]'),
            new Response(201, [], '[{"id":6789,"state":"queued"}]'),
            new Response(201, [], '{"id":1212,"notes":"test"}'),
            new Response(200, [], '{"id":"1212","notes":"test"}'),
            new Response(200, ['X-Total-Count' => 15, 'Link' => '<https://api.invoiced.com/invoices/456/notes?per_page=25&page=1>; rel="self", <https://api.invoiced.com/invoices/456/notes?per_page=25&page=1>; rel="first", <https://api.invoiced.com/invoices/456/notes?per_page=25&page=1>; rel="last"'], '[{"id":1212,"notes":"test"}]'),
        ]);

        self::$invoiced = new Client('API_KEY', false, null, $mock);
    }

    /**
     * @return void
     */
    public function testPay()
    {
        $invoice = new Invoice(self::$invoiced, 123);
        $this->assertTrue($invoice->pay());
        $this->assertTrue($invoice->paid);
    }

    /**
     * @return void
     */
    public function testCreatePaymentPlan()
    {
        $invoice = new Invoice(self::$invoiced, 456);
        $paymentPlan = $invoice->paymentPlan()->create(['date' => 1234, 'amount' => 100]);

        $this->assertInstanceOf('Invoiced\\PaymentPlan', $paymentPlan);
        $this->assertEquals(123, $paymentPlan->id);
        $this->assertEquals('active', $paymentPlan->status);
        $this->assertEquals('/invoices/456/payment_plan', $paymentPlan->getEndpoint());
    }

    /**
     * @return void
     */
    public function testRetrievePaymentPlan()
    {
        $invoice = new Invoice(self::$invoiced, 456);
        $paymentPlan = $invoice->paymentPlan()->get();

        $this->assertInstanceOf('Invoiced\\PaymentPlan', $paymentPlan);
        $this->assertEquals(123, $paymentPlan->id);
        $this->assertEquals('active', $paymentPlan->status);
        $this->assertEquals('/invoices/456/payment_plan', $paymentPlan->getEndpoint());
    }

    /**
     * @return void
     */
    public function testSendSMS()
    {
        $invoice = new Invoice(self::$invoiced, 123);
        $textMessages = $invoice->sendSMS();

        $this->assertTrue(is_array($textMessages));
        $this->assertCount(1, $textMessages);
        $this->assertInstanceOf('Invoiced\\TextMessage', $textMessages[0]);
        $this->assertEquals(5678, $textMessages[0]->id);
    }

    /**
     * @return void
     */
    public function testSendLetter()
    {
        $invoice = new Invoice(self::$invoiced, 123);
        $letters = $invoice->sendLetter();

        $this->assertTrue(is_array($letters));
        $this->assertCount(1, $letters);
        $this->assertInstanceOf('Invoiced\\Letter', $letters[0]);
        $this->assertEquals(6789, $letters[0]->id);
    }

    /**
     * @return void
     */
    public function testCreateNote()
    {
        $invoice = new Invoice(self::$invoiced, 456);
        $note = $invoice->notes()->create(['notes' => 'test']);

        $this->assertInstanceOf('Invoiced\\Note', $note);
        $this->assertEquals(1212, $note->id);
        $this->assertEquals('test', $note->notes);
        $this->assertEquals('/invoices/456/notes/1212', $note->getEndpoint());
    }

    /**
     * @return void
     */
    public function testRetrieveNote()
    {
        $invoice = new Invoice(self::$invoiced, 456);
        $note = $invoice->notes()->retrieve(1212);

        $this->assertInstanceOf('Invoiced\\Note', $note);
        $this->assertEquals(1212, $note->id);
        $this->assertEquals('test', $note->notes);
        $this->assertEquals('/invoices/456/notes/1212', $note->getEndpoint());
    }

    /**
     * @return void
     */
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
}
