<?php

use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\Psr7\Response;
use Invoiced\Client;
use Invoiced\Customer;

class CustomerTest extends PHPUnit_Framework_TestCase
{
    public static $invoiced;

    public static function setUpBeforeClass()
    {
        $mock = new MockHandler([
            new Response(201, [], '{"id":123,"name":"Pied Piper"}'),
            new Response(200, [], '{"id":"123","name":"Pied Piper"}'),
            new Response(200, [], '{"id":123,"name":"Pied Piper","notes":"Terrible customer"}'),
            new Response(401),
            new Response(200, ['X-Total-Count' => 15, 'Link' => '<https://api.invoiced.com/customers?per_page=25&page=1>; rel="self", <https://api.invoiced.com/customers?per_page=25&page=1>; rel="first", <https://api.invoiced.com/customers?per_page=25&page=1>; rel="last"'], '[{"id":123,"name":"Pied Piper"}]'),
            new Response(204),
            new Response(201, [], '[{"id":4567,"email":"test@example.com"}]'),
            new Response(200, [], '{"total_outstanding":1000,"available_credits":0,"past_due":true}'),
            new Response(201, [], '{"id":123,"name":"Nancy"}'),
            new Response(200, [], '{"id":"123","name":"Nancy"}'),
            new Response(200, ['X-Total-Count' => 10, 'Link' => '<https://api.invoiced.com/customers/123/line_items?per_page=25&page=1>; rel="self", <https://api.invoiced.com/customers/123/line_items?per_page=25&page=1>; rel="first", <https://api.invoiced.com/customers/123/line_items?per_page=25&page=1>; rel="last"'], '[{"id":123,"name":"Nancy"}]'),
            new Response(201, [], '{"id":123,"unit_cost":500}'),
            new Response(200, [], '{"id":"123","unit_cost":500}'),
            new Response(200, ['X-Total-Count' => 10, 'Link' => '<https://api.invoiced.com/customers/123/line_items?per_page=25&page=1>; rel="self", <https://api.invoiced.com/customers/123/line_items?per_page=25&page=1>; rel="first", <https://api.invoiced.com/customers/123/line_items?per_page=25&page=1>; rel="last"'], '[{"id":123,"unit_cost":500}]'),
            new Response(201, [], '{"id":456,"total":500}'),
            new Response(201, [], '[{"id":5678,"message":"test"}]'),
            new Response(201, [], '[{"id":6789,"state":"queued"}]'),
            new Response(201, [], '{"id":1212,"notes":"test"}'),
            new Response(200, [], '{"id":"1212","notes":"test"}'),
            new Response(200, ['X-Total-Count' => 15, 'Link' => '<https://api.invoiced.com/customers/123/notes?per_page=25&page=1>; rel="self", <https://api.invoiced.com/customers/123/notes?per_page=25&page=1>; rel="first", <https://api.invoiced.com/customers/123/notes?per_page=25&page=1>; rel="last"'], '[{"id":1212,"notes":"test"}]'),
            new Response(201, [], '{"id":123456,"total":1000}'),
        ]);

        self::$invoiced = new Client('API_KEY', false, false, $mock);
    }

    public function testGetEndpoint()
    {
        $customer = new Customer(self::$invoiced, 123);
        $this->assertEquals('/customers/123', $customer->getEndpoint());
    }

    public function testCreate()
    {
        $customer = self::$invoiced->Customer->create(['name' => 'Pied Piper']);

        $this->assertInstanceOf('Invoiced\\Customer', $customer);
        $this->assertEquals(123, $customer->id);
        $this->assertEquals('Pied Piper', $customer->name);
    }

    public function testRetrieveNoId()
    {
        $this->setExpectedException('InvalidArgumentException');
        self::$invoiced->Customer->retrieve(false);
    }

    public function testRetrieve()
    {
        $customer = self::$invoiced->Customer->retrieve(123);

        $this->assertInstanceOf('Invoiced\\Customer', $customer);
        $this->assertEquals(123, $customer->id);
        $this->assertEquals('Pied Piper', $customer->name);
    }

    public function testUpdateNoValue()
    {
        $customer = new Customer(self::$invoiced, 123);
        $this->assertFalse($customer->save());
    }

    public function testUpdate()
    {
        $customer = new Customer(self::$invoiced, 123);
        $customer->notes = 'Terrible customer';
        $this->assertTrue($customer->save());

        $this->assertEquals('Terrible customer', $customer->notes);
    }

    public function testUpdateFail()
    {
        $this->setExpectedException('Invoiced\\Error\\ApiError');

        $customer = new Customer(self::$invoiced, 123);
        $customer->notes = 'Great customer';
        $customer->save();
    }

    public function testAll()
    {
        list($customers, $metadata) = self::$invoiced->Customer->all();

        $this->assertTrue(is_array($customers));
        $this->assertCount(1, $customers);
        $this->assertEquals(123, $customers[0]->id);

        $this->assertInstanceOf('Invoiced\\Collection', $metadata);
        $this->assertEquals(15, $metadata->total_count);
    }

    public function testDelete()
    {
        $customer = new Customer(self::$invoiced, 123);
        $this->assertTrue($customer->delete());
    }

    public function testSendStatement()
    {
        $customer = new Customer(self::$invoiced, 123);
        $emails = $customer->sendStatement();

        $this->assertTrue(is_array($emails));
        $this->assertCount(1, $emails);
        $this->assertInstanceOf('Invoiced\\Email', $emails[0]);
        $this->assertEquals(4567, $emails[0]->id);
    }

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

    public function testCreateContact()
    {
        $customer = new Customer(self::$invoiced, 456);
        $contact = $customer->contacts()->create(['name' => 'Nancy']);

        $this->assertInstanceOf('Invoiced\\Contact', $contact);
        $this->assertEquals(123, $contact->id);
        $this->assertEquals('Nancy', $contact->name);
        $this->assertEquals('/customers/456/contacts/123', $contact->getEndpoint());
    }

    public function testRetrieveContact()
    {
        $customer = new Customer(self::$invoiced, 456);
        $contact = $customer->contacts()->retrieve(123);

        $this->assertInstanceOf('Invoiced\\Contact', $contact);
        $this->assertEquals(123, $contact->id);
        $this->assertEquals('Nancy', $contact->name);
        $this->assertEquals('/customers/456/contacts/123', $contact->getEndpoint());
    }

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

    public function testCreatePendingLineItem()
    {
        $customer = new Customer(self::$invoiced, 456);
        $lineItem = $customer->lineItems()->create(['unit_cost' => 500]);

        $this->assertInstanceOf('Invoiced\\LineItem', $lineItem);
        $this->assertEquals(123, $lineItem->id);
        $this->assertEquals(500, $lineItem->unit_cost);
        $this->assertEquals('/customers/456/line_items/123', $lineItem->getEndpoint());
    }

    public function testRetrievePendingLineItem()
    {
        $customer = new Customer(self::$invoiced, 456);
        $lineItem = $customer->lineItems()->retrieve(123);

        $this->assertInstanceOf('Invoiced\\LineItem', $lineItem);
        $this->assertEquals(123, $lineItem->id);
        $this->assertEquals(500, $lineItem->unit_cost);
        $this->assertEquals('/customers/456/line_items/123', $lineItem->getEndpoint());
    }

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

    public function testInvoice()
    {
        $customer = new Customer(self::$invoiced, 456);
        $invoice = $customer->invoice();

        $this->assertInstanceOf('Invoiced\\Invoice', $invoice);
        $this->assertEquals(456, $invoice->id);
        $this->assertEquals(500, $invoice->total);
    }

    public function testSendStatementSMS()
    {
        $customer = new Customer(self::$invoiced, 123);
        $textMessages = $customer->sendStatementSMS();

        $this->assertTrue(is_array($textMessages));
        $this->assertCount(1, $textMessages);
        $this->assertInstanceOf('Invoiced\\TextMessage', $textMessages[0]);
        $this->assertEquals(5678, $textMessages[0]->id);
    }

    public function testSendStatementLetter()
    {
        $customer = new Customer(self::$invoiced, 123);
        $letters = $customer->sendStatementLetter();

        $this->assertTrue(is_array($letters));
        $this->assertCount(1, $letters);
        $this->assertInstanceOf('Invoiced\\Letter', $letters[0]);
        $this->assertEquals(6789, $letters[0]->id);
    }

    public function testCreateNote()
    {
        $customer = new Customer(self::$invoiced, 456);
        $note = $customer->notes()->create(['notes' => 'test']);

        $this->assertInstanceOf('Invoiced\\Note', $note);
        $this->assertEquals(1212, $note->id);
        $this->assertEquals('test', $note->notes);
        $this->assertEquals('/customers/456/notes/1212', $note->getEndpoint());
    }

    public function testRetrieveNote()
    {
        $customer = new Customer(self::$invoiced, 456);
        $note = $customer->notes()->retrieve(1212);

        $this->assertInstanceOf('Invoiced\\Note', $note);
        $this->assertEquals(1212, $note->id);
        $this->assertEquals('test', $note->notes);
        $this->assertEquals('/customers/456/notes/1212', $note->getEndpoint());
    }

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

    public function testConsolidateInvoices()
    {
        $customer = new Customer(self::$invoiced, 123);
        $invoice = $customer->consolidateInvoices();

        $this->assertInstanceOf('Invoiced\\Invoice', $invoice);
        $this->assertEquals(123456, $invoice->id);
        $this->assertEquals(1000, $invoice->total);
    }
}