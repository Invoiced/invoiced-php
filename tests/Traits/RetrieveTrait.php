<?php

namespace Invoiced\Tests\Traits;

use GuzzleHttp\Psr7\Response;

trait RetrieveTrait
{
    /**
     * @return void
     */
    public function testRetrieveNoId()
    {
        $this->expectException('InvalidArgumentException');
        $client = $this->makeClient();
        $class = self::OBJECT_CLASS;
        (new $class($client))->retrieve('');
    }

    /**
     * @return void
     */
    public function testRetrieve()
    {
        $client = $this->makeClient(new Response(200, [], '{"id":456}'));

        $class = self::OBJECT_CLASS;
        $obj = (new $class($client))->retrieve(456);

        $this->assertInstanceOf($class, $obj);
        $this->assertEquals(456, $obj->id);
    }
}
