<?php

namespace Invoiced\Tests\Traits;

use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\Psr7\Response;
use Invoiced\Client;

trait UpdateTrait
{
    /**
     * @return void
     */
    public function testUpdateNoValue()
    {
        $client = $this->makeClient();
        $class = self::OBJECT_CLASS;
        $obj = new $class($client, 456, []);
        $this->assertFalse($obj->save());
    }

    /**
     * @return void
     */
    public function testUpdate()
    {
        $client = $this->makeClient(new Response(200, [], '{"id":456,"name":"Nancy Drew"}'));

        $class = self::OBJECT_CLASS;
        $obj = new $class($client, 456, []);
        $obj->name = 'Nancy Drew';
        $this->assertTrue($obj->save());

        $this->assertEquals('Nancy Drew', $obj->name);
    }

    /**
     * @return void
     */
    public function testUpdateFail()
    {
        $this->setExpectedException('Invoiced\\Error\\ApiError');

        $client = $this->makeClient(new Response(401));
        $class = self::OBJECT_CLASS;
        $obj = new $class($client, 456, []);
        $obj->name = 'Nancy Drew';
        $obj->save();
    }
}