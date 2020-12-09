<?php

namespace Invoiced\Tests;

use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\Psr7\Response;
use Invoiced\Client;
use Invoiced\Coupon;
use PHPUnit_Framework_TestCase;

class CouponTest extends PHPUnit_Framework_TestCase
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
            new Response(200, [], '{"id":"test","name":"Some Coupon"}'),
            new Response(401),
            new Response(200, ['X-Total-Count' => 15, 'Link' => '<https://api.invoiced.com/coupons?per_page=25&page=1>; rel="self", <https://api.invoiced.com/coupons?per_page=25&page=1>; rel="first", <https://api.invoiced.com/coupons?per_page=25&page=1>; rel="last"'], '[{"id":"test","name":"Some Item"}]'),
            new Response(204),
        ]);

        self::$invoiced = new Client('API_KEY', false, null, $mock);
    }

    /**
     * @return void
     */
    public function testGetEndpoint()
    {
        $coupon = new Coupon(self::$invoiced, 'test');
        $this->assertEquals('/coupons/test', $coupon->getEndpoint());
    }

    /**
     * @return void
     */
    public function testCreate()
    {
        $coupon = self::$invoiced->Coupon->create(['id' => 'test', 'name' => 'Test']);

        $this->assertInstanceOf('Invoiced\\Coupon', $coupon);
        $this->assertEquals('test', $coupon->id);
        $this->assertEquals('Test', $coupon->name);
    }

    /**
     * @return void
     */
    public function testRetrieveNoId()
    {
        $this->setExpectedException('InvalidArgumentException');
        self::$invoiced->Coupon->retrieve('');
    }

    /**
     * @return void
     */
    public function testRetrieve()
    {
        $coupon = self::$invoiced->Coupon->retrieve('test');
    }

    /**
     * @return void
     */
    public function testUpdateNoValue()
    {
        $coupon = new Coupon(self::$invoiced, 'test');
        $this->assertFalse($coupon->save());
    }

    /**
     * @return void
     */
    public function testUpdate()
    {
        $coupon = new Coupon(self::$invoiced, 'test');
        $coupon->name = 'Some Item';
        $this->assertTrue($coupon->save());
    }

    /**
     * @return void
     */
    public function testUpdateFail()
    {
        $this->setExpectedException('Invoiced\\Error\\ApiError');

        $coupon = new Coupon(self::$invoiced, 'test');
        $coupon->name = 'Test';
        $coupon->save();
    }

    /**
     * @return void
     */
    public function testAll()
    {
        list($coupons, $metadata) = self::$invoiced->Coupon->all();

        $this->assertTrue(is_array($coupons));
        $this->assertCount(1, $coupons);
        $this->assertEquals('test', $coupons[0]->id);

        $this->assertInstanceOf('Invoiced\\Collection', $metadata);
        $this->assertEquals(15, $metadata->total_count);
    }

    /**
     * @return void
     */
    public function testDelete()
    {
        $coupon = new Coupon(self::$invoiced, 'test');
        $this->assertTrue($coupon->delete());
    }
}
