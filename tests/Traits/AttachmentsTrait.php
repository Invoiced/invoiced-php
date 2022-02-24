<?php

namespace Invoiced\Tests\Traits;

use GuzzleHttp\Psr7\Response;

trait AttachmentsTrait
{
    /**
     * @return void
     */
    public function testAttachments()
    {
        $client = $this->makeClient(new Response(200, ['X-Total-Count' => '10', 'Link' => '<https://api.invoiced.com/objects/123/attachments?per_page=25&page=1>; rel="self", <https://api.invoiced.com/objects/123/attachments?per_page=25&page=1>; rel="first", <https://api.invoiced.com/objects/123/attachments?per_page=25&page=1>; rel="last"'], '[{"file":{"id":456}}]'));

        $class = self::OBJECT_CLASS;
        list($attachments, $metadata) = (new $class($client, 123))->attachments(); /* @phpstan-ignore-line */

        $this->assertTrue(is_array($attachments));
        $this->assertCount(1, $attachments);
        $this->assertEquals(456, $attachments[0]->id);
        $this->assertInstanceOf('Invoiced\\Collection', $metadata);
        $this->assertEquals(10, $metadata->total_count);
    }
}
