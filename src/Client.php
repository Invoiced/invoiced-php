<?php

namespace Invoiced;

use InvalidArgumentException;
use GuzzleHttp\Client as GuzzleClient;
use GuzzleHttp\Exception\RequestException;
use GuzzleHttp\HandlerStack;

class Client
{
    const API_BASE = 'https://api.invoiced.com';
    const API_BASE_SANDBOX = 'https://api.sandbox.invoiced.com';

    const VERSION = '0.2.0';

    public $Customer;
    public $Invoice;
    public $Transaction;
    public $Subscription;

    /**
     * @var string
     */
    private $apiKey;

    /**
     * @var bool
     */
    private $sandbox;

    /**
     * @var GuzzleHttp\Client
     */
    private $client;

    /**
     * Instantiates a new client.
     *
     * @param string             $apiKey
     * @param bool               $sandbox when true uses the sandbox API endpoint
     * @param GuzzleHttp\Handler $handler
     */
    public function __construct($apiKey, $sandbox = false, $handler = null)
    {
        if (empty($apiKey)) {
            throw new InvalidArgumentException('You must provide an API Key');
        }

        // add any custom Guzzle HTTP handlers (useful for mocking)
        $handlerStack = ($handler) ? HandlerStack::create($handler) : HandlerStack::create();

        $this->apiKey = $apiKey;
        $this->sandbox = $sandbox;

        $this->client = new GuzzleClient([
            'base_uri' => $this->endpoint(),
            'handler' => $handlerStack,
        ]);

        $this->Customer = new Customer($this);
        $this->Invoice = new Invoice($this);
        $this->Transaction = new Transaction($this);
        $this->Subscription = new Subscription($this);
    }

    public function apiKey()
    {
        return $this->apiKey;
    }

    public function endpoint()
    {
        return $this->sandbox ? self::API_BASE_SANDBOX : self::API_BASE;
    }

    public function request($method, $endpoint, $params = [])
    {
        $method = strtolower($method);

        $headers = [
            'Content-Type' => 'application/json',
            'User-Agent' => 'Invoiced PHP/'.self::VERSION,
        ];

        $request = [
            'auth' => [$this->apiKey, ''],
            'headers' => $headers,
            'query' => [],
            'http_errors' => false,
        ];

        // these methods have no request body
        if ($method === 'get' || $method === 'delete') {
            $request['query'] = array_replace($request['query'], $params);
        } else {
            $request['json'] = $params;
        }

        try {
            // perform the request
            $response = $this->client->$method($endpoint, $request);
        } catch (RequestException $e) {
            throw new Error\ApiConnectionError('There was an error connecting to the Invoiced API. The reason was: '.$e->getMessage());
        }

        // validate response
        $code = $response->getStatusCode();
        $body = $response->getBody();

        if ($code >= 200 && $code < 400) {
            $parsed = null;

            // expect a JSON response unless we received 204 No Content
            if ($code != 204) {
                $parsed = json_decode($body, true);

                if ($parsed === null) {
                    throw new Error\ApiError("Could not decode JSON of $code response: $body", $code);
                }
            }

            $parsedHeaders = [];
            foreach ($response->getHeaders() as $k => $v) {
                $parsedHeaders[$k] = implode(', ', $v);
            }

            return [
                'code' => $code,
                'headers' => $parsedHeaders,
                'body' => $parsed,
            ];
        } else {
            $error = json_decode($body, true);

            if ($error === null) {
                throw $this->generalApiError($code, $body);
            }

            if ($code == 401) {
                throw new Error\AuthenticationError($error['message'], $code, $error);
            } elseif (in_array($code, [400, 403, 404])) {
                throw new Error\InvalidRequest($error['message'], $code, $error);
            } elseif ($code == 500) {
                throw new Error\ApiError($error['message'], $code, $error);
            } else {
                throw $this->generalApiError($code, $body);
            }
        }

        return $response;
    }

    private function generalApiError($code, $body)
    {
        return new Error\ApiError("API Error $code - $body", $code);
    }
}
