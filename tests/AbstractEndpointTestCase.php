<?php

namespace Invoiced\Tests;

use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\Psr7\Response;
use Invoiced\Client;
use Yoast\PHPUnitPolyfills\TestCases\TestCase;

abstract class AbstractEndpointTestCase extends TestCase
{
    /**
     * @param Response|null $response
     *
     * @return Client
     */
    protected function makeClient($response = null)
    {
        if (!$response) {
            return new Client('API_KEY');
        }

        return new Client('API_KEY', false, null, new MockHandler([$response]));
    }
}
