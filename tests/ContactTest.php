<?php

namespace Invoiced\Tests;

use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\Psr7\Response;
use Invoiced\Client;
use Invoiced\Contact;
use PHPUnit_Framework_TestCase;

class ContactTest extends PHPUnit_Framework_TestCase
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
            new Response(201, [], '{"id":456,"name":"Nancy"}'),
            new Response(200, [], '{"id":456,"name":"Nancy"}'),
            new Response(200, [], '{"id":456,"name":"Nancy Drew"}'),
            new Response(401),
            new Response(200, ['X-Total-Count' => 15, 'Link' => '<https://api.invoiced.com/contacts?per_page=25&page=1>; rel="self", <https://api.invoiced.com/contacts?per_page=25&page=1>; rel="first", <https://api.invoiced.com/contacts?per_page=25&page=1>; rel="last"'], '[{"id":456,"name":"Nancy"}]'),
            new Response(204),
        ]);

        self::$invoiced = new Client('API_KEY', false, null, $mock);
    }

    /**
     * @return void
     */
    public function testGetEndpoint()
    {
        $contact = new Contact(self::$invoiced, 123);
        $this->assertEquals('/contacts/123', $contact->getEndpoint());
    }

    /**
     * @return void
     */
    public function testCreate()
    {
        $contact = new Contact(self::$invoiced, null, []);
        $contact = $contact->create(['name' => 'Nancy']);

        $this->assertInstanceOf('Invoiced\\Contact', $contact);
        $this->assertEquals(456, $contact->id);
        $this->assertEquals('Nancy', $contact->name);
    }

    /**
     * @return void
     */
    public function testRetrieveNoId()
    {
        $this->setExpectedException('InvalidArgumentException');
        $contact = new Contact(self::$invoiced, null, []);
        $contact->retrieve('');
    }

    /**
     * @return void
     */
    public function testRetrieve()
    {
        $contact = new Contact(self::$invoiced, null, []);
        $contact = $contact->retrieve(456);

        $this->assertInstanceOf('Invoiced\\Contact', $contact);
        $this->assertEquals(456, $contact->id);
        $this->assertEquals('Nancy', $contact->name);
    }

    /**
     * @return void
     */
    public function testUpdateNoValue()
    {
        $contact = new Contact(self::$invoiced, 456, []);
        $this->assertFalse($contact->save());
    }

    /**
     * @return void
     */
    public function testUpdate()
    {
        $contact = new Contact(self::$invoiced, 456, []);
        $contact->name = 'Nancy Drew';
        $this->assertTrue($contact->save());

        $this->assertEquals('Nancy Drew', $contact->name);
    }

    /**
     * @return void
     */
    public function testUpdateFail()
    {
        $this->setExpectedException('Invoiced\\Error\\ApiError');

        $contact = new Contact(self::$invoiced, 456, []);
        $contact->name = 'Nancy Drew';
        $contact->save();
    }

    /**
     * @return void
     */
    public function testAll()
    {
        $contact = new Contact(self::$invoiced, 456, []);
        list($contacts, $metadata) = $contact->all();

        $this->assertTrue(is_array($contacts));
        $this->assertCount(1, $contacts);
        $this->assertEquals(456, $contacts[0]->id);

        $this->assertInstanceOf('Invoiced\\Collection', $metadata);
        $this->assertEquals(15, $metadata->total_count);
    }

    /**
     * @return void
     */
    public function testDelete()
    {
        $contact = new Contact(self::$invoiced, 456, []);
        $this->assertTrue($contact->delete());
    }
}
