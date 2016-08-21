<?php

use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\Psr7\Response;
use Invoiced\Client;
use Invoiced\Subscription;

class SubscriptionTest extends PHPUnit_Framework_TestCase
{
    public static $invoiced;

    public static function setUpBeforeClass()
    {
        $mock = new MockHandler([
            new Response(201, [], '{"id":123,"plan":"starter"}'),
            new Response(200, [], '{"id":123,"plan":"starter"}'),
            new Response(200, [], '{"id":123,"plan":"pro"}'),
            new Response(401),
            new Response(200, ['X-Total-Count' => 15, 'Link' => '<https://api.invoiced.com/subscriptions?per_page=25&page=1>; rel="self", <https://api.invoiced.com/subscriptions?per_page=25&page=1>; rel="first", <https://api.invoiced.com/subscriptions?per_page=25&page=1>; rel="last"'], '[{"id":123,"plan":"pro"}]'),
            new Response(200, [], '{"id":123,"plan":"pro","status":"canceled"}'),
        ]);

        self::$invoiced = new Client('API_KEY', false, $mock);
    }

    public function testGetEndpoint()
    {
        $subscription = new Subscription(self::$invoiced, 123);
        $this->assertEquals('/subscriptions/123', $subscription->getEndpoint());
    }

    public function testCreate()
    {
        $subscription = self::$invoiced->Subscription->create(['customer' => 123, 'plan' => 'starter']);

        $this->assertInstanceOf('Invoiced\\Subscription', $subscription);
        $this->assertEquals(123, $subscription->id);
        $this->assertEquals('starter', $subscription->plan);
    }

    public function testRetrieveNoId()
    {
        $this->setExpectedException('InvalidArgumentException');
        self::$invoiced->Subscription->retrieve(false);
    }

    public function testRetrieve()
    {
        $subscription = self::$invoiced->Subscription->retrieve(123);
    }

    public function testUpdateNoValue()
    {
        $subscription = new Subscription(self::$invoiced, 123);
        $this->assertFalse($subscription->save());
    }

    public function testUpdate()
    {
        $subscription = new Subscription(self::$invoiced, 123);
        $subscription->plan = 'pro';
        $this->assertTrue($subscription->save());
    }

    public function testUpdateFail()
    {
        $this->setExpectedException('Invoiced\\Error\\ApiError');

        $subscription = new Subscription(self::$invoiced, 123);
        $subscription->plan = 'starter';
        $subscription->save();
    }

    public function testAll()
    {
        list($subscriptions, $metadata) = self::$invoiced->Subscription->all();

        $this->assertTrue(is_array($subscriptions));
        $this->assertCount(1, $subscriptions);
        $this->assertEquals(123, $subscriptions[0]->id);

        $this->assertInstanceOf('Invoiced\\Collection', $metadata);
        $this->assertEquals(15, $metadata->total_count);
    }

    public function testCancel()
    {
        $subscription = new Subscription(self::$invoiced, 123);
        $this->assertTrue($subscription->cancel());
        $this->assertEquals('canceled', $subscription->status);
    }
}
