<?php

use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\Psr7\Response;
use Invoiced\Client;
use Invoiced\Item;

class ItemTest extends PHPUnit_Framework_TestCase
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
            new Response(200, ['X-Total-Count' => 15, 'Link' => '<https://api.invoiced.com/items?per_page=25&page=1>; rel="self", <https://api.invoiced.com/items?per_page=25&page=1>; rel="first", <https://api.invoiced.com/items?per_page=25&page=1>; rel="last"'], '[{"id":"test","name":"Some Item"}]'),
            new Response(204),
        ]);

        self::$invoiced = new Client('API_KEY', false, null, $mock);
    }

    /**
     * @return void
     */
    public function testGetEndpoint()
    {
        $item = new Item(self::$invoiced, 'test');
        $this->assertEquals('/items/test', $item->getEndpoint());
    }

    /**
     * @return void
     */
    public function testCreate()
    {
        $item = self::$invoiced->Item->create(['id' => 'test', 'name' => 'Test']);

        $this->assertInstanceOf('Invoiced\\Item', $item);
        $this->assertEquals('test', $item->id);
        $this->assertEquals('Test', $item->name);
    }

    /**
     * @return void
     */
    public function testRetrieveNoId()
    {
        $this->setExpectedException('InvalidArgumentException');
        self::$invoiced->Item->retrieve('');
    }

    /**
     * @return void
     */
    public function testRetrieve()
    {
        $item = self::$invoiced->Item->retrieve('test');
    }

    /**
     * @return void
     */
    public function testUpdateNoValue()
    {
        $item = new Item(self::$invoiced, 'test');
        $this->assertFalse($item->save());
    }

    /**
     * @return void
     */
    public function testUpdate()
    {
        $item = new Item(self::$invoiced, 'test');
        $item->name = 'Some Item';
        $this->assertTrue($item->save());
    }

    /**
     * @return void
     */
    public function testUpdateFail()
    {
        $this->setExpectedException('Invoiced\\Error\\ApiError');

        $item = new Item(self::$invoiced, 'test');
        $item->name = 'Test';
        $item->save();
    }

    /**
     * @return void
     */
    public function testAll()
    {
        list($items, $metadata) = self::$invoiced->Item->all();

        $this->assertTrue(is_array($items));
        $this->assertCount(1, $items);
        $this->assertEquals('test', $items[0]->id);

        $this->assertInstanceOf('Invoiced\\Collection', $metadata);
        $this->assertEquals(15, $metadata->total_count);
    }

    /**
     * @return void
     */
    public function testDelete()
    {
        $item = new Item(self::$invoiced, 'test');
        $this->assertTrue($item->delete());
    }
}
