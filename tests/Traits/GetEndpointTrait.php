<?php

namespace Invoiced\Tests\Traits;

trait GetEndpointTrait
{
    /**
     * @return void
     */
    public function testGetEndpoint()
    {
        $client = $this->makeClient();
        $class = static::OBJECT_CLASS;
        $this->assertEquals(static::EXPECTED_ENDPOINT, (new $class($client, 123))->getEndpoint());
    }
}
