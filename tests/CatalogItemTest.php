<?php

use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\Psr7\Response;
use Invoiced\CatalogItem;
use Invoiced\Client;

class CatalogItemTest extends PHPUnit_Framework_TestCase
{
    public static $invoiced;

    public static function setUpBeforeClass()
    {
        $mock = new MockHandler([
            new Response(201, [], '{"id":"test","name":"Test"}'),
            new Response(200, [], '{"id":"test","name":"Test"}'),
            new Response(200, [], '{"id":"test","name":"Some Item"}'),
            new Response(401),
            new Response(200, ['X-Total-Count' => 15, 'Link' => '<https://api.invoiced.com/catalog_items?per_page=25&page=1>; rel="self", <https://api.invoiced.com/catalog_items?per_page=25&page=1>; rel="first", <https://api.invoiced.com/catalog_items?per_page=25&page=1>; rel="last"'], '[{"id":"test","name":"Some Item"}]'),
            new Response(204),
        ]);

        self::$invoiced = new Client('API_KEY', false, $mock);
    }

    public function testGetEndpoint()
    {
        $catalogItem = new CatalogItem(self::$invoiced, 'test');
        $this->assertEquals('/catalog_items/test', $catalogItem->getEndpoint());
    }

    public function testCreate()
    {
        $catalogItem = self::$invoiced->CatalogItem->create(['id' => 'test', 'name' => 'Test']);

        $this->assertInstanceOf('Invoiced\\CatalogItem', $catalogItem);
        $this->assertEquals('test', $catalogItem->id);
        $this->assertEquals('Test', $catalogItem->name);
    }

    public function testRetrieveNoId()
    {
        $this->setExpectedException('InvalidArgumentException');
        self::$invoiced->CatalogItem->retrieve(false);
    }

    public function testRetrieve()
    {
        $catalogItem = self::$invoiced->CatalogItem->retrieve('test');
    }

    public function testUpdateNoValue()
    {
        $catalogItem = new CatalogItem(self::$invoiced, 'test');
        $this->assertFalse($catalogItem->save());
    }

    public function testUpdate()
    {
        $catalogItem = new CatalogItem(self::$invoiced, 'test');
        $catalogItem->name = 'Some Item';
        $this->assertTrue($catalogItem->save());
    }

    public function testUpdateFail()
    {
        $this->setExpectedException('Invoiced\\Error\\ApiError');

        $catalogItem = new CatalogItem(self::$invoiced, 'test');
        $catalogItem->name = 'Test';
        $catalogItem->save();
    }

    public function testAll()
    {
        list($catalog_items, $metadata) = self::$invoiced->CatalogItem->all();

        $this->assertTrue(is_array($catalog_items));
        $this->assertCount(1, $catalog_items);
        $this->assertEquals('test', $catalog_items[0]->id);

        $this->assertInstanceOf('Invoiced\\Collection', $metadata);
        $this->assertEquals(15, $metadata->total_count);
    }

    public function testDelete()
    {
        $catalogItem = new CatalogItem(self::$invoiced, 'test');
        $this->assertTrue($catalogItem->delete());
    }
}
