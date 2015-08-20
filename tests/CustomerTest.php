<?php

use Invoiced\Client;
use Invoiced\Customer;

class CustomerTest extends PHPUnit_Framework_TestCase
{
    public static $invoiced;

    public static function setUpBeforeClass()
    {
        self::$invoiced = new Client('test');
    }

    public function testCreate()
    {
        $customer = self::$invoiced->Customer->create(['name' => 'Pied Piper']);
    }

    public function testRetrieve()
    {
        $customer = self::$invoiced->Customer->retrieve('test');
    }

    public function testUpdate()
    {
        $customer = new Customer(self::$invoiced, 'test');
        $customer->notes = 'Terrible customer';
        $this->assertTrue($customer->save());
    }

    public function testAll()
    {
        list($customers, $metadata) = self::$invoiced->Customer->all();
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
    }

    public function testBalance()
    {
        $customer = new Customer(self::$invoiced, 'test');

        $expected = new stdClass();
        $expected->past_due = false;
        $expected->available_credits = 0;
        $expected->total_outstanding = 0;

        $this->assertEquals($expected, $customer->balance());
    }

    public function testSubscriptions()
    {
        $customer = new Customer(self::$invoiced, 'test');
        list($subscriptions, $metadata) = $customer->subscriptions();
    }
}
