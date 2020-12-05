<?php

use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\Psr7\Response;
use Invoiced\Client;
use Invoiced\CreditNote;

class CreditNoteTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var Client
     */
    public static $invoiced;

    /**
     * @return void
     */
    public static function setUpBeforeClass()
    {
        $mock = new MockHandler([
            new Response(201, [], '{"id":123,"number":"CN-0001"}'),
            new Response(200, [], '{"id":123,"number":"CN-0001"}'),
            new Response(200, [], '{"id":123,"closed":true}'),
            new Response(401),
            new Response(200, ['X-Total-Count' => 15, 'Link' => '<https://api.invoiced.com/credit_notes?per_page=25&page=1>; rel="self", <https://api.invoiced.com/credit_notes?per_page=25&page=1>; rel="first", <https://api.invoiced.com/credit_notes?per_page=25&page=1>; rel="last"'], '[{"id":123,"number":"CN-0001"}]'),
            new Response(204),
            new Response(201, [], '[{"id":4567,"email":"test@example.com"}]'),
            new Response(200, ['X-Total-Count' => 10, 'Link' => '<https://api.invoiced.com/credit_notes/123/attachments?per_page=25&page=1>; rel="self", <https://api.invoiced.com/credit_notes/123/attachments?per_page=25&page=1>; rel="first", <https://api.invoiced.com/credit_notes/123/attachments?per_page=25&page=1>; rel="last"'], '[{"file":{"id":456}}]'),
            new Response(200, [], '{"id":"1234","status":"voided"}'),
        ]);

        self::$invoiced = new Client('API_KEY', false, null, $mock);
    }

    /**
     * @return void
     */
    public function testGetEndpoint()
    {
        $creditNote = new CreditNote(self::$invoiced, 123);
        $this->assertEquals('/credit_notes/123', $creditNote->getEndpoint());
    }

    /**
     * @return void
     */
    public function testCreate()
    {
        $creditNote = self::$invoiced->CreditNote->create(['customer' => 123]);

        $this->assertInstanceOf('Invoiced\\CreditNote', $creditNote);
        $this->assertEquals(123, $creditNote->id);
        $this->assertEquals('CN-0001', $creditNote->number);
    }

    /**
     * @return void
     */
    public function testRetrieveNoId()
    {
        $this->setExpectedException('InvalidArgumentException');
        self::$invoiced->CreditNote->retrieve('');
    }

    /**
     * @return void
     */
    public function testRetrieve()
    {
        $creditNote = self::$invoiced->CreditNote->retrieve(123);

        $this->assertInstanceOf('Invoiced\\CreditNote', $creditNote);
        $this->assertEquals(123, $creditNote->id);
        $this->assertEquals('CN-0001', $creditNote->number);
    }

    /**
     * @return void
     */
    public function testUpdateNoValue()
    {
        $creditNote = new CreditNote(self::$invoiced, 123);
        $this->assertFalse($creditNote->save());
    }

    /**
     * @return void
     */
    public function testUpdate()
    {
        $creditNote = new CreditNote(self::$invoiced, 123);
        $creditNote->closed = true;
        $this->assertTrue($creditNote->save());

        $this->assertTrue($creditNote->closed);
    }

    /**
     * @return void
     */
    public function testUpdateFail()
    {
        $this->setExpectedException('Invoiced\\Error\\ApiError');

        $creditNote = new CreditNote(self::$invoiced, 123);
        $creditNote->closed = true;
        $creditNote->save();
    }

    /**
     * @return void
     */
    public function testAll()
    {
        list($creditNotes, $metadata) = self::$invoiced->CreditNote->all();

        $this->assertTrue(is_array($creditNotes));
        $this->assertCount(1, $creditNotes);
        $this->assertEquals(123, $creditNotes[0]->id);

        $this->assertInstanceOf('Invoiced\\Collection', $metadata);
        $this->assertEquals(15, $metadata->total_count);
    }

    /**
     * @return void
     */
    public function testDelete()
    {
        $creditNote = new CreditNote(self::$invoiced, 123);
        $this->assertTrue($creditNote->delete());
    }

    /**
     * @return void
     */
    public function testSend()
    {
        $creditNote = new CreditNote(self::$invoiced, 123);
        $emails = $creditNote->send();

        $this->assertTrue(is_array($emails));
        $this->assertCount(1, $emails);
        $this->assertInstanceOf('Invoiced\\Email', $emails[0]);
        $this->assertEquals(4567, $emails[0]->id);
    }

    /**
     * @return void
     */
    public function testAttachments()
    {
        $creditNote = new CreditNote(self::$invoiced, 123);
        list($attachments, $metadata) = $creditNote->attachments();
        $this->assertTrue(is_array($attachments));
        $this->assertCount(1, $attachments);
        $this->assertEquals(456, $attachments[0]->id);
        $this->assertInstanceOf('Invoiced\\Collection', $metadata);
        $this->assertEquals(10, $metadata->total_count);
    }

    /**
     * @return void
     */
    public function testVoid()
    {
        $creditNote = new CreditNote(self::$invoiced, 1234);
        $creditNote->void();

        $this->assertInstanceOf('Invoiced\\CreditNote', $creditNote);
        $this->assertEquals(1234, $creditNote->id);
        $this->assertEquals('voided', $creditNote->status);
    }
}
