<?php

namespace Invoiced\Tests\Traits;

use GuzzleHttp\Psr7\Response;

trait ListTrait
{
    /**
     * @return void
     */
    public function testAll()
    {
        $client = $this->makeClient(new Response(200, ['X-Total-Count' => 15, 'Link' => '<https://api.invoiced.com/objects?per_page=25&page=1>; rel="self", <https://api.invoiced.com/objects?per_page=25&page=1>; rel="first", <https://api.invoiced.com/objects?per_page=25&page=1>; rel="last"'], '[{"id":123}]'));

        $class = static::OBJECT_CLASS;
        list($objects, $metadata) = (new $class($client))->all();

        $this->assertTrue(is_array($objects));
        $this->assertCount(1, $objects);
        $this->assertEquals(123, $objects[0]->id);

        $this->assertInstanceOf('Invoiced\\Collection', $metadata);
        $this->assertEquals(15, $metadata->total_count);
    }
}
