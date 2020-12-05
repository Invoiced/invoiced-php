<?php

namespace Invoiced\Tests;

use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\Psr7\Response;
use Invoiced\Client;
use Invoiced\Subscription;
use PHPUnit_Framework_TestCase;

class SubscriptionTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var Client
     */
    public static $invoiced;

    /**
     * @return void
     */
    public static function setUpBeforeClass()
    {
        $mock = new MockHandler([
            new Response(201, [], '{"id":123,"plan":"starter"}'),
            new Response(200, [], '{"id":123,"plan":"starter"}'),
            new Response(200, [], '{"id":123,"plan":"pro"}'),
            new Response(401),
            new Response(200, ['X-Total-Count' => 15, 'Link' => '<https://api.invoiced.com/subscriptions?per_page=25&page=1>; rel="self", <https://api.invoiced.com/subscriptions?per_page=25&page=1>; rel="first", <https://api.invoiced.com/subscriptions?per_page=25&page=1>; rel="last"'], '[{"id":123,"plan":"pro"}]'),
            new Response(200, [], '{"id":123,"plan":"pro","status":"canceled"}'),
            new Response(200, [], '{"first_invoice":{"id":false},"mrr":9}'),
        ]);

        self::$invoiced = new Client('API_KEY', false, null, $mock);
    }

    /**
     * @return void
     */
    public function testGetEndpoint()
    {
        $subscription = new Subscription(self::$invoiced, 123);
        $this->assertEquals('/subscriptions/123', $subscription->getEndpoint());
    }

    /**
     * @return void
     */
    public function testCreate()
    {
        $subscription = self::$invoiced->Subscription->create(['customer' => 123, 'plan' => 'starter']);

        $this->assertInstanceOf('Invoiced\\Subscription', $subscription);
        $this->assertEquals(123, $subscription->id);
        $this->assertEquals('starter', $subscription->plan);
    }

    /**
     * @return void
     */
    public function testRetrieveNoId()
    {
        $this->setExpectedException('InvalidArgumentException');
        self::$invoiced->Subscription->retrieve('');
    }

    /**
     * @return void
     */
    public function testRetrieve()
    {
        $subscription = self::$invoiced->Subscription->retrieve(123);
    }

    /**
     * @return void
     */
    public function testUpdateNoValue()
    {
        $subscription = new Subscription(self::$invoiced, 123);
        $this->assertFalse($subscription->save());
    }

    /**
     * @return void
     */
    public function testUpdate()
    {
        $subscription = new Subscription(self::$invoiced, 123);
        $subscription->plan = 'pro';
        $this->assertTrue($subscription->save());
    }

    /**
     * @return void
     */
    public function testUpdateFail()
    {
        $this->setExpectedException('Invoiced\\Error\\ApiError');

        $subscription = new Subscription(self::$invoiced, 123);
        $subscription->plan = 'starter';
        $subscription->save();
    }

    /**
     * @return void
     */
    public function testAll()
    {
        list($subscriptions, $metadata) = self::$invoiced->Subscription->all();

        $this->assertTrue(is_array($subscriptions));
        $this->assertCount(1, $subscriptions);
        $this->assertEquals(123, $subscriptions[0]->id);

        $this->assertInstanceOf('Invoiced\\Collection', $metadata);
        $this->assertEquals(15, $metadata->total_count);
    }

    /**
     * @return void
     */
    public function testCancel()
    {
        $subscription = new Subscription(self::$invoiced, 123);
        $this->assertTrue($subscription->cancel());
        $this->assertEquals('canceled', $subscription->status);
    }

    /**
     * @return void
     */
    public function testPreview()
    {
        $subscription = new Subscription(self::$invoiced, 123);
        $response = $subscription->preview();

        // we do not expect subscription object; just json
        $this->assertNotInstanceOf('Invoiced\\Subscription', $response);
        // but json should be formed correctly
        $this->assertEquals($response->mrr, 9); /* @phpstan-ignore-line */
    }
}
