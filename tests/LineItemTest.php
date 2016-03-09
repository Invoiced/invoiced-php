<?php

use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\Psr7\Response;
use Invoiced\Client;
use Invoiced\LineItem;

class LineItemTest extends PHPUnit_Framework_TestCase
{
    public static $invoiced;

    public static function setUpBeforeClass()
    {
        $mock = new MockHandler([
            new Response(201, [], '{"id":456,"amount":500}'),
            new Response(200, [], '{"id":456,"amount":500}'),
            new Response(200, [], '{"id":456,"amount":600}'),
            new Response(401),
            new Response(200, ['X-Total-Count' => 15, 'Link' => '<https://api.invoiced.com/line_items?per_page=25&page=1>; rel="self", <https://api.invoiced.com/line_items?per_page=25&page=1>; rel="first", <https://api.invoiced.com/line_items?per_page=25&page=1>; rel="last"'], '[{"id":456,"amount":500}]'),
            new Response(204),
        ]);

        self::$invoiced = new Client('API_KEY', false, $mock);
    }

    public function testGetEndpoint()
    {
        $line = new LineItem(self::$invoiced, 123);
        $this->assertEquals('/line_items/123', $line->getEndpoint());
    }

    public function testCreate()
    {
        $line = new LineItem(self::$invoiced, null, []);
        $lineItem = $line->create(['amount' => 500]);

        $this->assertInstanceOf('Invoiced\\LineItem', $lineItem);
        $this->assertEquals(456, $lineItem->id);
        $this->assertEquals(500, $lineItem->amount);
    }

    public function testRetrieveNoId()
    {
        $this->setExpectedException('InvalidArgumentException');
        $lineItem = new LineItem(self::$invoiced, null, []);
        $lineItem->retrieve(false);
    }

    public function testRetrieve()
    {
        $line = new LineItem(self::$invoiced, null, []);
        $lineItem = $line->retrieve(456);

        $this->assertInstanceOf('Invoiced\\LineItem', $lineItem);
        $this->assertEquals(456, $lineItem->id);
        $this->assertEquals(500, $lineItem->amount);
    }

    public function testUpdateNoValue()
    {
        $lineItem = new LineItem(self::$invoiced, 456, []);
        $this->assertFalse($lineItem->save());
    }

    public function testUpdate()
    {
        $lineItem = new LineItem(self::$invoiced, 456, []);
        $lineItem->amount = 600;
        $this->assertTrue($lineItem->save());

        $this->assertEquals(600, $lineItem->amount);
    }

    public function testUpdateFail()
    {
        $this->setExpectedException('Invoiced\\Error\\ApiError');

        $lineItem = new LineItem(self::$invoiced, 456, []);
        $lineItem->amount = 600;
        $lineItem->save();
    }

    public function testAll()
    {
        $lineItem = new LineItem(self::$invoiced, 456, []);
        list($lineItems, $metadata) = $lineItem->all();

        $this->assertTrue(is_array($lineItems));
        $this->assertCount(1, $lineItems);
        $this->assertEquals(456, $lineItems[0]->id);

        $this->assertInstanceOf('Invoiced\\Collection', $metadata);
        $this->assertEquals(15, $metadata->total_count);
    }

    public function testDelete()
    {
        $lineItem = new LineItem(self::$invoiced, 456, []);
        $this->assertTrue($lineItem->delete());
    }
}
