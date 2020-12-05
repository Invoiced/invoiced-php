<?php

use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\Psr7\Response;
use Invoiced\Client;
use Invoiced\Event;

class EventTest extends PHPUnit_Framework_TestCase
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
            new Response(200, ['X-Total-Count' => 15, 'Link' => '<https://api.invoiced.com/events?per_page=25&page=1>; rel="self", <https://api.invoiced.com/events?per_page=25&page=1>; rel="first", <https://api.invoiced.com/events?per_page=25&page=1>; rel="last"'], '[{"id":123,"type":"customer.created"}]'),
        ]);

        self::$invoiced = new Client('API_KEY', false, null, $mock);
    }

    /**
     * @return void
     */
    public function testGetEndpoint()
    {
        $event = new Event(self::$invoiced, 123);
        $this->assertEquals('/events/123', $event->getEndpoint());
    }

    /**
     * @return void
     */
    public function testAll()
    {
        list($events, $metadata) = self::$invoiced->Event->all();

        $this->assertTrue(is_array($events));
        $this->assertCount(1, $events);
        $this->assertEquals(123, $events[0]->id);

        $this->assertInstanceOf('Invoiced\\Collection', $metadata);
        $this->assertEquals(15, $metadata->total_count);
    }
}
