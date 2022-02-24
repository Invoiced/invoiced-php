<?php

namespace Invoiced\Tests\Traits;

use GuzzleHttp\Psr7\Response;
use Invoiced\BaseObject;

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
        $this->assertFalse($obj->save()); /* @phpstan-ignore-line */
    }

    /**
     * @return void
     */
    public function testUpdate()
    {
        $client = $this->makeClient(new Response(200, [], '{"id":456,"name":"Nancy Drew"}'));

        $class = self::OBJECT_CLASS;
        $obj = new $class($client, 456, []);
        $obj->name = 'Nancy Drew'; /* @phpstan-ignore-line */
        $this->assertTrue($obj->save()); /* @phpstan-ignore-line */

        $this->assertEquals('Nancy Drew', $obj->name);
    }

    /**
     * @return void
     */
    public function testUpdateFail()
    {
        $this->expectException('Invoiced\\Error\\ApiError');

        $client = $this->makeClient(new Response(401));
        $class = self::OBJECT_CLASS;
        $obj = new $class($client, 456, []);
        $obj->name = 'Nancy Drew'; /* @phpstan-ignore-line */
        $obj->save(); /* @phpstan-ignore-line */
    }
}
