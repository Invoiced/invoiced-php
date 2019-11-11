<?php

use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\Psr7\Response;
use Invoiced\Client;
use Invoiced\Estimate;

class EstimateTest extends PHPUnit_Framework_TestCase
{
    public static $invoiced;

    public static function setUpBeforeClass()
    {
        $mock = new MockHandler([
            new Response(201, [], '{"id":123,"number":"CN-0001"}'),
            new Response(200, [], '{"id":123,"number":"CN-0001"}'),
            new Response(200, [], '{"id":123,"closed":true}'),
            new Response(401),
            new Response(200, ['X-Total-Count' => 15, 'Link' => '<https://api.invoiced.com/estimates?per_page=25&page=1>; rel="self", <https://api.invoiced.com/estimates?per_page=25&page=1>; rel="first", <https://api.invoiced.com/estimates?per_page=25&page=1>; rel="last"'], '[{"id":123,"number":"CN-0001"}]'),
            new Response(204),
            new Response(201, [], '[{"id":4567,"email":"test@example.com"}]'),
            new Response(200, ['X-Total-Count' => 10, 'Link' => '<https://api.invoiced.com/estimates/123/attachments?per_page=25&page=1>; rel="self", <https://api.invoiced.com/estimates/123/attachments?per_page=25&page=1>; rel="first", <https://api.invoiced.com/estimates/123/attachments?per_page=25&page=1>; rel="last"'], '[{"file":{"id":456}}]'),
            new Response(201, [], '{"id":456,"total":500}'),
            new Response(200, [], '{"id":"1234","status":"voided"}'),
        ]);

        self::$invoiced = new Client('API_KEY', false, false, $mock);
    }

    public function testGetEndpoint()
    {
        $estimate = new Estimate(self::$invoiced, 123);
        $this->assertEquals('/estimates/123', $estimate->getEndpoint());
    }

    public function testCreate()
    {
        $estimate = self::$invoiced->Estimate->create(['customer' => 123]);

        $this->assertInstanceOf('Invoiced\\Estimate', $estimate);
        $this->assertEquals(123, $estimate->id);
        $this->assertEquals('CN-0001', $estimate->number);
    }

    public function testRetrieveNoId()
    {
        $this->setExpectedException('InvalidArgumentException');
        self::$invoiced->Estimate->retrieve(false);
    }

    public function testRetrieve()
    {
        $estimate = self::$invoiced->Estimate->retrieve(123);

        $this->assertInstanceOf('Invoiced\\Estimate', $estimate);
        $this->assertEquals(123, $estimate->id);
        $this->assertEquals('CN-0001', $estimate->number);
    }

    public function testUpdateNoValue()
    {
        $estimate = new Estimate(self::$invoiced, 123);
        $this->assertFalse($estimate->save());
    }

    public function testUpdate()
    {
        $estimate = new Estimate(self::$invoiced, 123);
        $estimate->closed = true;
        $this->assertTrue($estimate->save());

        $this->assertTrue($estimate->closed);
    }

    public function testUpdateFail()
    {
        $this->setExpectedException('Invoiced\\Error\\ApiError');

        $estimate = new Estimate(self::$invoiced, 123);
        $estimate->closed = true;
        $estimate->save();
    }

    public function testAll()
    {
        list($estimates, $metadata) = self::$invoiced->Estimate->all();

        $this->assertTrue(is_array($estimates));
        $this->assertCount(1, $estimates);
        $this->assertEquals(123, $estimates[0]->id);

        $this->assertInstanceOf('Invoiced\\Collection', $metadata);
        $this->assertEquals(15, $metadata->total_count);
    }

    public function testDelete()
    {
        $estimate = new Estimate(self::$invoiced, 123);
        $this->assertTrue($estimate->delete());
    }

    public function testSend()
    {
        $estimate = new Estimate(self::$invoiced, 123);
        $emails = $estimate->send();

        $this->assertTrue(is_array($emails));
        $this->assertCount(1, $emails);
        $this->assertInstanceOf('Invoiced\\Email', $emails[0]);
        $this->assertEquals(4567, $emails[0]->id);
    }

    public function testAttachments()
    {
        $estimate = new Estimate(self::$invoiced, 123);
        list($attachments, $metadata) = $estimate->attachments();
        $this->assertTrue(is_array($attachments));
        $this->assertCount(1, $attachments);
        $this->assertEquals(456, $attachments[0]->id);
        $this->assertInstanceOf('Invoiced\\Collection', $metadata);
        $this->assertEquals(10, $metadata->total_count);
    }

    public function testInvoice()
    {
        $estimate = new Estimate(self::$invoiced, 456);
        $invoice = $estimate->invoice();

        $this->assertInstanceOf('Invoiced\\Invoice', $invoice);
        $this->assertEquals(456, $invoice->id);
        $this->assertEquals(500, $invoice->total);
    }

    public function testVoid()
    {
        $estimate = new Estimate(self::$invoiced, 1234);
        $estimate->void();

        $this->assertInstanceOf('Invoiced\\Estimate', $estimate);
        $this->assertEquals(1234, $estimate->id);
        $this->assertEquals('voided', $estimate->status);
    }
}
