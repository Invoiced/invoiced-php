<?php

use Invoiced\Client;

class ClientTest extends PHPUnit_Framework_TestCase
{
	function testNoApiKey()
	{
		$this->setExpectedException('Exception');

		$client = new Client('');
	}

	function testApiKey()
	{
		$client = new Client('test');
		$this->assertEquals('test', $client->apiKey());
	}

	function testRequest()
	{
		$client = new Client('API_KEY');

		$response = $client->request('GET', '/invoices', ['per_page' => 3]);
		
		print_r($response);
	}
}