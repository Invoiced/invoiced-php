<?php

namespace Invoiced\Tests;

use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\Psr7\Response;
use Invoiced\Client;
use Invoiced\Customer;
use Invoiced\Tests\Traits\CreateTrait;
use Invoiced\Tests\Traits\DeleteTrait;
use Invoiced\Tests\Traits\GetEndpointTrait;
use Invoiced\Tests\Traits\ListTrait;
use Invoiced\Tests\Traits\RetrieveTrait;
use Invoiced\Tests\Traits\UpdateTrait;
use stdClass;

class CustomerTest extends AbstractEndpointTestCase
{
    use GetEndpointTrait;
    use CreateTrait;
    use RetrieveTrait;
    use UpdateTrait;
    use DeleteTrait;
    use ListTrait;

    const OBJECT_CLASS = 'Invoiced\\Customer';
    const EXPECTED_ENDPOINT = '/customers/123';

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
            new Response(201, [], '[{"id":4567,"email":"test@example.com"}]'),
            new Response(200, [], '{"total_outstanding":1000,"available_credits":0,"past_due":true}'),
            new Response(201, [], '{"id":123,"name":"Nancy"}'),
            new Response(200, [], '{"id":"123","name":"Nancy"}'),
            new Response(200, ['X-Total-Count' => '10', 'Link' => '<https://api.invoiced.com/customers/123/line_items?per_page=25&page=1>; rel="self", <https://api.invoiced.com/customers/123/line_items?per_page=25&page=1>; rel="first", <https://api.invoiced.com/customers/123/line_items?per_page=25&page=1>; rel="last"'], '[{"id":123,"name":"Nancy"}]'),
            new Response(201, [], '{"id":123,"unit_cost":500}'),
            new Response(200, [], '{"id":"123","unit_cost":500}'),
            new Response(200, ['X-Total-Count' => '10', 'Link' => '<https://api.invoiced.com/customers/123/line_items?per_page=25&page=1>; rel="self", <https://api.invoiced.com/customers/123/line_items?per_page=25&page=1>; rel="first", <https://api.invoiced.com/customers/123/line_items?per_page=25&page=1>; rel="last"'], '[{"id":123,"unit_cost":500}]'),
            new Response(201, [], '{"id":456,"total":500}'),
            new Response(201, [], '[{"id":5678,"message":"test"}]'),
            new Response(201, [], '[{"id":6789,"state":"queued"}]'),
            new Response(201, [], '{"id":1212,"notes":"test"}'),
            new Response(200, [], '{"id":"1212","notes":"test"}'),
            new Response(200, ['X-Total-Count' => '15', 'Link' => '<https://api.invoiced.com/customers/123/notes?per_page=25&page=1>; rel="self", <https://api.invoiced.com/customers/123/notes?per_page=25&page=1>; rel="first", <https://api.invoiced.com/customers/123/notes?per_page=25&page=1>; rel="last"'], '[{"id":1212,"notes":"test"}]'),
            new Response(201, [], '{"id":123456,"total":1000}'),
            new Response(201, [], '{"id":1231,"object":"card"}'),
            new Response(201, [], '{"id":2342,"object":"bank_account"}'),
            new Response(201, [], '{"id":121212,"object":"something_else"}'),
            new Response(200, ['X-Total-Count' => '15', 'Link' => '<https://api.invoiced.com/customers/123/payment_sources?per_page=25&page=1>; rel="self", <https://api.invoiced.com/customers/123/payment_sources?per_page=25&page=1>; rel="first", <https://api.invoiced.com/customers/123/payment_sources?per_page=25&page=1>; rel="last"'], '[{"id":1231,"object":"card"}, {"id":2342,"object":"bank_account"}]'),
        ]);

        self::$invoiced = new Client('API_KEY', false, null, $mock);
    }

    /**
     * @return void
     */
    public function testSendStatement()
    {
        $customer = new Customer(self::$invoiced, 123);
        $emails = $customer->sendStatement();

        $this->assertTrue(is_array($emails));
        $this->assertCount(1, $emails);
        $this->assertInstanceOf('Invoiced\\Email', $emails[0]);
        $this->assertEquals(4567, $emails[0]->id);
    }

    /**
     * @return void
     */
    public function testBalance()
    {
        $customer = new Customer(self::$invoiced, 123);

        $expected = new stdClass();
        $expected->past_due = true;
        $expected->available_credits = 0;
        $expected->total_outstanding = 1000;

        $balance = $customer->balance();
        $this->assertInstanceOf('stdClass', $balance);
        $this->assertEquals($expected, $balance);
    }

    /**
     * @return void
     */
    public function testCreateContact()
    {
        $customer = new Customer(self::$invoiced, 456);
        $contact = $customer->contacts()->create(['name' => 'Nancy']);

        $this->assertInstanceOf('Invoiced\\Contact', $contact);
        $this->assertEquals(123, $contact->id);
        $this->assertEquals('Nancy', $contact->name);
        $this->assertEquals('/customers/456/contacts/123', $contact->getEndpoint());
    }

    /**
     * @return void
     */
    public function testRetrieveContact()
    {
        $customer = new Customer(self::$invoiced, 456);
        $contact = $customer->contacts()->retrieve(123);

        $this->assertInstanceOf('Invoiced\\Contact', $contact);
        $this->assertEquals(123, $contact->id);
        $this->assertEquals('Nancy', $contact->name);
        $this->assertEquals('/customers/456/contacts/123', $contact->getEndpoint());
    }

    /**
     * @return void
     */
    public function testAllContacts()
    {
        $customer = new Customer(self::$invoiced, 456);
        list($contacts, $metadata) = $customer->contacts()->all();

        $this->assertTrue(is_array($contacts));
        $this->assertCount(1, $contacts);
        $this->assertEquals(123, $contacts[0]->id);
        $this->assertEquals('/customers/456/contacts/123', $contacts[0]->getEndpoint());

        $this->assertInstanceOf('Invoiced\\Collection', $metadata);
        $this->assertEquals(10, $metadata->total_count);
    }

    /**
     * @return void
     */
    public function testCreatePendingLineItem()
    {
        $customer = new Customer(self::$invoiced, 456);
        $lineItem = $customer->lineItems()->create(['unit_cost' => 500]);

        $this->assertInstanceOf('Invoiced\\LineItem', $lineItem);
        $this->assertEquals(123, $lineItem->id);
        $this->assertEquals(500, $lineItem->unit_cost);
        $this->assertEquals('/customers/456/line_items/123', $lineItem->getEndpoint());
    }

    /**
     * @return void
     */
    public function testRetrievePendingLineItem()
    {
        $customer = new Customer(self::$invoiced, 456);
        $lineItem = $customer->lineItems()->retrieve(123);

        $this->assertInstanceOf('Invoiced\\LineItem', $lineItem);
        $this->assertEquals(123, $lineItem->id);
        $this->assertEquals(500, $lineItem->unit_cost);
        $this->assertEquals('/customers/456/line_items/123', $lineItem->getEndpoint());
    }

    /**
     * @return void
     */
    public function testAllPendingLineItems()
    {
        $customer = new Customer(self::$invoiced, 456);
        list($lines, $metadata) = $customer->lineItems()->all();

        $this->assertTrue(is_array($lines));
        $this->assertCount(1, $lines);
        $this->assertEquals(123, $lines[0]->id);
        $this->assertEquals('/customers/456/line_items/123', $lines[0]->getEndpoint());

        $this->assertInstanceOf('Invoiced\\Collection', $metadata);
        $this->assertEquals(10, $metadata->total_count);
    }

    /**
     * @return void
     */
    public function testInvoice()
    {
        $customer = new Customer(self::$invoiced, 456);
        $invoice = $customer->invoice();

        $this->assertInstanceOf('Invoiced\\Invoice', $invoice);
        $this->assertEquals(456, $invoice->id);
        $this->assertEquals(500, $invoice->total);
    }

    /**
     * @return void
     */
    public function testSendStatementSMS()
    {
        $customer = new Customer(self::$invoiced, 123);
        $textMessages = $customer->sendStatementSMS();

        $this->assertTrue(is_array($textMessages));
        $this->assertCount(1, $textMessages);
        $this->assertInstanceOf('Invoiced\\TextMessage', $textMessages[0]);
        $this->assertEquals(5678, $textMessages[0]->id);
    }

    /**
     * @return void
     */
    public function testSendStatementLetter()
    {
        $customer = new Customer(self::$invoiced, 123);
        $letters = $customer->sendStatementLetter();

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
        $customer = new Customer(self::$invoiced, 456);
        $note = $customer->notes()->create(['notes' => 'test']);

        $this->assertInstanceOf('Invoiced\\Note', $note);
        $this->assertEquals(1212, $note->id);
        $this->assertEquals('test', $note->notes);
        $this->assertEquals('/customers/456/notes/1212', $note->getEndpoint());
    }

    /**
     * @return void
     */
    public function testRetrieveNote()
    {
        $customer = new Customer(self::$invoiced, 456);
        $note = $customer->notes()->retrieve(1212);

        $this->assertInstanceOf('Invoiced\\Note', $note);
        $this->assertEquals(1212, $note->id);
        $this->assertEquals('test', $note->notes);
        $this->assertEquals('/customers/456/notes/1212', $note->getEndpoint());
    }

    /**
     * @return void
     */
    public function testAllNotes()
    {
        $customer = new Customer(self::$invoiced, 456);
        list($notes, $metadata) = $customer->notes()->all();

        $this->assertTrue(is_array($notes));
        $this->assertCount(1, $notes);
        $this->assertEquals(1212, $notes[0]->id);
        $this->assertEquals('/customers/456/notes/1212', $notes[0]->getEndpoint());

        $this->assertInstanceOf('Invoiced\\Collection', $metadata);
        $this->assertEquals(15, $metadata->total_count);
    }

    /**
     * @return void
     */
    public function testConsolidateInvoices()
    {
        $customer = new Customer(self::$invoiced, 123);
        $invoice = $customer->consolidateInvoices();

        $this->assertInstanceOf('Invoiced\\Invoice', $invoice);
        $this->assertEquals(123456, $invoice->id);
        $this->assertEquals(1000, $invoice->total);
    }

    /**
     * @return void
     */
    public function testCreateCard()
    {
        $customer = new Customer(self::$invoiced, 456);
        $card = $customer->paymentSources()->create(['method' => 'credit_card']);

        $this->assertInstanceOf('Invoiced\\Card', $card);
        $this->assertEquals(1231, $card->id);
        $this->assertEquals('card', $card->object);
        $this->assertEquals('/customers/456/cards/1231', $card->getEndpoint());
    }

    /**
     * @return void
     */
    public function testCreateBankAccount()
    {
        $customer = new Customer(self::$invoiced, 456);
        $account = $customer->paymentSources()->create(['method' => 'ach']);

        $this->assertInstanceOf('Invoiced\\BankAccount', $account);
        $this->assertEquals(2342, $account->id);
        $this->assertEquals('/customers/456/bank_accounts/2342', $account->getEndpoint());
    }

    /**
     * @return void
     */
    public function testCreateGenericPaymentSource()
    {
        $customer = new Customer(self::$invoiced, 456);
        $source = $customer->paymentSources()->create(['method' => 'something_else']);

        $this->assertInstanceOf('Invoiced\\PaymentSource', $source);
        $this->assertEquals(121212, $source->id);
        $this->assertEquals('/customers/456/payment_sources/121212', $source->getEndpoint());
    }

    /**
     * @return void
     */
    public function testAllPaymentSources()
    {
        $customer = new Customer(self::$invoiced, 456);
        list($sources, $metadata) = $customer->paymentSources()->all();

        $this->assertTrue(is_array($sources));
        $this->assertCount(2, $sources);
        $this->assertEquals(1231, $sources[0]->id);
        $this->assertEquals('/customers/456/cards/1231', $sources[0]->getEndpoint());
        $this->assertEquals('/customers/456/bank_accounts/2342', $sources[1]->getEndpoint());

        $this->assertInstanceOf('Invoiced\\Card', $sources[0]);
        $this->assertInstanceOf('Invoiced\\BankAccount', $sources[1]);

        $this->assertInstanceOf('Invoiced\\Collection', $metadata);
        $this->assertEquals(15, $metadata->total_count);
    }
}
