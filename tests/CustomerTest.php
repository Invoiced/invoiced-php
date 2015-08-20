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
            new Response(200, ['X-Total-Count' => 10, 'Link' => '<https://api.invoiced.com/customers/123/subscriptions?per_page=25&page=1>; rel="self", <https://api.invoiced.comcustomers/123/subscriptions?per_page=25&page=1>; rel="first", <https://api.invoiced.comcustomers/123/subscriptions?per_page=25&page=1>; rel="last"'], '[{"id":123,"plan":456}]'),
        ]);

        self::$invoiced = new Client('API_KEY', $mock);
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

    public function testUpdate()
    {
        $customer = new Customer(self::$invoiced, 'test');
        $customer->notes = 'Terrible customer';
        $this->assertTrue($customer->save());

        $this->assertEquals('Terrible customer', $customer->notes);
    }

    public function testUpdateFail()
    {
        $this->setExpectedException('Invoiced\\Error\\ApiError');

        $customer = new Customer(self::$invoiced, 'test');
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
        $customer = new Customer(self::$invoiced, 'test');
        $this->assertTrue($customer->delete());
    }

    public function testSendStatement()
    {
        $customer = new Customer(self::$invoiced, 'test');
        $emails = $customer->sendStatement();

        $this->assertTrue(is_array($emails));
        $this->assertCount(1, $emails);
        $this->assertEquals(4567, $emails[0]->id);
    }

    public function testBalance()
    {
        $customer = new Customer(self::$invoiced, 'test');

        $expected = new stdClass();
        $expected->past_due = true;
        $expected->available_credits = 0;
        $expected->total_outstanding = 1000;

        $balance = $customer->balance();
        $this->assertInstanceOf('stdClass', $balance);
        $this->assertEquals($expected, $balance);
    }

    public function testSubscriptions()
    {
        $customer = new Customer(self::$invoiced, 'test');
        list($subscriptions, $metadata) = $customer->subscriptions();

        $this->assertTrue(is_array($subscriptions));
        $this->assertCount(1, $subscriptions);
        $this->assertEquals(123, $subscriptions[0]->id);

        $this->assertInstanceOf('Invoiced\\Collection', $metadata);
        $this->assertEquals(10, $metadata->total_count);
    }
}
