<?php

use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\Psr7\Response;
use Invoiced\Client;
use Invoiced\Plan;

class PlanTest extends PHPUnit_Framework_TestCase
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
            new Response(201, [], '{"id":"test","name":"Test"}'),
            new Response(200, [], '{"id":"test","name":"Test"}'),
            new Response(200, [], '{"id":"test","name":"Some Item"}'),
            new Response(401),
            new Response(200, ['X-Total-Count' => 15, 'Link' => '<https://api.invoiced.com/plans?per_page=25&page=1>; rel="self", <https://api.invoiced.com/plans?per_page=25&page=1>; rel="first", <https://api.invoiced.com/plans?per_page=25&page=1>; rel="last"'], '[{"id":"test","name":"Some Item"}]'),
            new Response(204),
        ]);

        self::$invoiced = new Client('API_KEY', false, null, $mock);
    }

    /**
     * @return void
     */
    public function testGetEndpoint()
    {
        $plan = new Plan(self::$invoiced, 'test');
        $this->assertEquals('/plans/test', $plan->getEndpoint());
    }

    /**
     * @return void
     */
    public function testCreate()
    {
        $plan = self::$invoiced->Plan->create(['id' => 'test', 'name' => 'Test']);

        $this->assertInstanceOf('Invoiced\\Plan', $plan);
        $this->assertEquals('test', $plan->id);
        $this->assertEquals('Test', $plan->name);
    }

    /**
     * @return void
     */
    public function testRetrieveNoId()
    {
        $this->setExpectedException('InvalidArgumentException');
        self::$invoiced->Plan->retrieve('');
    }

    /**
     * @return void
     */
    public function testRetrieve()
    {
        $plan = self::$invoiced->Plan->retrieve('test');
    }

    /**
     * @return void
     */
    public function testUpdateNoValue()
    {
        $plan = new Plan(self::$invoiced, 'test');
        $this->assertFalse($plan->save());
    }

    /**
     * @return void
     */
    public function testUpdate()
    {
        $plan = new Plan(self::$invoiced, 'test');
        $plan->name = 'Some Item';
        $this->assertTrue($plan->save());
    }

    /**
     * @return void
     */
    public function testUpdateFail()
    {
        $this->setExpectedException('Invoiced\\Error\\ApiError');

        $plan = new Plan(self::$invoiced, 'test');
        $plan->name = 'Test';
        $plan->save();
    }

    /**
     * @return void
     */
    public function testAll()
    {
        list($plans, $metadata) = self::$invoiced->Plan->all();

        $this->assertTrue(is_array($plans));
        $this->assertCount(1, $plans);
        $this->assertEquals('test', $plans[0]->id);

        $this->assertInstanceOf('Invoiced\\Collection', $metadata);
        $this->assertEquals(15, $metadata->total_count);
    }

    /**
     * @return void
     */
    public function testDelete()
    {
        $plan = new Plan(self::$invoiced, 'test');
        $this->assertTrue($plan->delete());
    }
}
