<?php

namespace Invoiced\Tests\Traits;

use GuzzleHttp\Psr7\Response;

trait VoidTrait
{
    /**
     * @return void
     */
    public function testVoid()
    {
        $client = $this->makeClient(new Response(200, [], '{"id":"1234","status":"voided"}'));
        $class = self::OBJECT_CLASS;
        $obj = new $class($client, 1234, []);
        $this->assertTrue($obj->void()); /* @phpstan-ignore-line */
        $this->assertEquals('voided', $obj->status);
    }
}
