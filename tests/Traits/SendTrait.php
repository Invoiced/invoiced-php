<?php

namespace Invoiced\Tests\Traits;

use GuzzleHttp\Psr7\Response;

trait SendTrait
{
    /**
     * @return void
     */
    public function testSend()
    {
        $client = $this->makeClient(new Response(201, [], '[{"id":4567,"email":"test@example.com"}]'));

        $class = self::OBJECT_CLASS;
        $emails = (new $class($client, 123))->send(); /* @phpstan-ignore-line */

        $this->assertTrue(is_array($emails));
        $this->assertCount(1, $emails);
        $this->assertInstanceOf('Invoiced\\Email', $emails[0]);
        $this->assertEquals(4567, $emails[0]->id);
    }
}
