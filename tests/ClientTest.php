<?php

use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\Psr7\Request;
use GuzzleHttp\Psr7\Response;
use GuzzleHttp\Exception\RequestException;
use Invoiced\Client;

class ClientTest extends PHPUnit_Framework_TestCase
{
    public function testNoApiKey()
    {
        $this->setExpectedException('InvalidArgumentException');

        $client = new Client('');
    }

    public function testApiKey()
    {
        $client = new Client('test');
        $this->assertEquals('test', $client->apiKey());
    }

    public function testRequest()
    {
        $mock = new MockHandler([
            new Response(200, ['X-Foo' => 'Bar'], '{"test":true}'),
        ]);

        $client = new Client('API_KEY', $mock);

        $response = $client->request('GET', '/invoices', ['per_page' => 3]);

        $expected = [
            'code' => 200,
            'headers' => [
                'X-Foo' => 'Bar',
            ],
            'body' => [
                'test' => 1,
            ],
        ];
        $this->assertEquals($expected, $response);
    }

    public function testRequestPost()
    {
        $mock = new MockHandler([
            new Response(201, ['X-Foo' => 'Bar'], '{"test":true}'),
        ]);

        $client = new Client('API_KEY', $mock);

        $response = $client->request('POST', '/invoices', ['customer' => 123]);

        $expected = [
            'code' => 201,
            'headers' => [
                'X-Foo' => 'Bar',
            ],
            'body' => [
                'test' => 1,
            ],
        ];
        $this->assertEquals($expected, $response);
    }

    public function testRequestInvalidJson()
    {
        $this->setExpectedException('Invoiced\\Error\\ApiError');

        $mock = new MockHandler([
            new Response(200, [], 'not valid json'),
        ]);

        $client = new Client('API_KEY', $mock);

        $client->request('GET', '/invoices');
    }

    public function testRequestAuthError()
    {
        $this->setExpectedException('Invoiced\\Error\\AuthenticationError');

        $mock = new MockHandler([
            new Response(401, [], '{"error":"invalid_request","message":"invalid api key"}'),
        ]);

        $client = new Client('API_KEY', $mock);

        $client->request('GET', '/invoices');
    }

    public function testRequestInvalid()
    {
        $this->setExpectedException('Invoiced\\Error\\InvalidRequest');

        $mock = new MockHandler([
            new Response(400, [], '{"error":"invalid_request","message":"not found"}'),
        ]);

        $client = new Client('API_KEY', $mock);

        $client->request('GET', '/invoices');
    }

    public function testRequestApiError()
    {
        $this->setExpectedException('Invoiced\\Error\\ApiError');

        $mock = new MockHandler([
            new Response(500, [], '{"error":"api","message":"idk"}'),
        ]);

        $client = new Client('API_KEY', $mock);

        $client->request('GET', '/invoices');
    }

    public function testRequestGeneralApiError()
    {
        $this->setExpectedException('Invoiced\\Error\\ApiError');

        $mock = new MockHandler([
            new Response(502, [], '{"error":"api","message":"idk"}'),
        ]);

        $client = new Client('API_KEY', $mock);

        $client->request('GET', '/invoices');
    }

    public function testRequestApiErrorInvalidJson()
    {
        $this->setExpectedException('Invoiced\\Error\\ApiError');

        $mock = new MockHandler([
            new Response(500, [], 'not valid json'),
        ]);

        $client = new Client('API_KEY', $mock);

        $client->request('GET', '/invoices');
    }

    public function testRequestConnectionError()
    {
        $this->setExpectedException('Invoiced\\Error\\ApiConnectionError');

        $request = new Request('GET', 'https://api.invoiced.com');
        $mock = new MockHandler([
            new RequestException('Could not connect', $request),
        ]);

        $client = new Client('API_KEY', $mock);

        $client->request('GET', '/invoices');
    }
}
