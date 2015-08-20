<?php

use Invoiced\Client;
use Invoiced\Object;

class ObjectTest extends PHPUnit_Framework_TestCase
{
    public static $invoiced;

    public static function setUpBeforeClass()
    {
        self::$invoiced = new Client('API_KEY');
    }

    public function testGetClient()
    {
        $object = new Object(self::$invoiced, 123);
        $this->assertEquals(self::$invoiced, $object->getClient());
    }

    public function testMagic()
    {
        $this->setExpectedException('Exception');

        $object = new Object(self::$invoiced, 123);

        $object->test = 'blah';
        $this->assertEquals('blah', $object->test);
        $this->assertTrue(isset($object->test));
        unset($object->test);
        $this->assertNull($object->test);
    }

    public function testToString()
    {
        $object = new Object(self::$invoiced, 123);
        $this->assertEquals("Invoiced\\Object JSON: {\n    \"id\": 123\n}", (string) $object);
    }

    public function testArrayAccess()
    {
        $object = new Object(self::$invoiced, 123);
        $object['test'] = 'blah';
        $this->assertEquals('blah', $object['test']);
        $this->assertTrue(isset($object['test']));
        $this->assertEquals(['id', 'test'], $object->keys());
        unset($object['test']);
        $this->assertNull($object['test']);
    }

    public function testJsonSerializable()
    {
        $object = new Object(self::$invoiced, 123, ['test' => true]);

        $expected = [
            'id' => 123,
            'test' => true,
        ];
        $this->assertEquals($expected, $object->jsonSerialize());

        $expected = "{
    \"test\": true,
    \"id\": 123
}";
        $this->assertEquals($expected, $object->__toJSON());
    }

    public function testCannotSetEmpty()
    {
        $this->setExpectedException('InvalidArgumentException');

        $object = new Object(self::$invoiced, 123);
        $object->test = '';
    }

    public function testCannotSetPermanent()
    {
        $object = new Object(self::$invoiced, 123);
        $object->id = 456;

        $this->assertEquals(123, $object->id);
    }
}
