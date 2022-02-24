<?php

namespace Invoiced\Tests\Traits;

use Invoiced\BaseObject;

trait GetEndpointTrait
{
    /**
     * @return void
     */
    public function testGetEndpoint()
    {
        $client = $this->makeClient();
        /** @var BaseObject $class */
        $class = static::OBJECT_CLASS;
        $this->assertEquals(static::EXPECTED_ENDPOINT, (new $class($client, 123))->getEndpoint());
    }
}
