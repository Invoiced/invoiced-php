<?php

namespace Invoiced\Tests\Traits;

use GuzzleHttp\Psr7\Response;
use Invoiced\BaseObject;

trait DeleteTrait
{
    /**
     * @return void
     */
    public function testDelete()
    {
        $client = $this->makeClient(new Response(204));
        $class = self::OBJECT_CLASS;
        $obj = new $class($client, 456, []);
        $this->assertTrue($obj->delete()); /* @phpstan-ignore-line */
    }
}
