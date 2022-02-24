<?php

namespace Invoiced\Tests\Traits;

use GuzzleHttp\Psr7\Response;
use Invoiced\BaseObject;

trait RetrieveTrait
{
    /**
     * @return void
     */
    public function testRetrieveNoId()
    {
        $this->expectException('InvalidArgumentException');
        $client = $this->makeClient();
        /** @var BaseObject $class */
        $class = self::OBJECT_CLASS;
        (new $class($client))->retrieve('');
    }

    /**
     * @return void
     */
    public function testRetrieve()
    {
        $client = $this->makeClient(new Response(200, [], '{"id":456}'));

        /** @var BaseObject $class */
        $class = self::OBJECT_CLASS;
        $obj = (new $class($client))->retrieve(456);

        $this->assertInstanceOf(self::OBJECT_CLASS, $obj);
        $this->assertEquals(456, $obj->id);
    }
}
