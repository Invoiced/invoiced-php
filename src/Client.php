<?php

namespace Invoiced;

use Firebase\JWT\JWT;
use GuzzleHttp\Client as GuzzleClient;
use GuzzleHttp\Exception\RequestException;
use GuzzleHttp\HandlerStack;
use InvalidArgumentException;

class Client
{
    const API_BASE = 'https://api.invoiced.com';
    const API_BASE_SANDBOX = 'https://api.sandbox.invoiced.com';

    const VERSION = '0.12.0';

    const CONNECT_TIMEOUT = 30;
    const READ_TIMEOUT = 60;

    public $CatalogItem;
    public $CreditNote;
    public $Customer;
    public $Estimate;
    public $Event;
    public $File;
    public $Invoice;
    public $Plan;
    public $Subscription;
    public $Transaction;

    /**
     * @var string
     */
    private $apiKey;

    /**
     * @var bool
     */
    private $sandbox;

    /**
     * @var string
     */
    private $ssoKey;

    /**
     * @var string
     */
    private $caBundleFile;

    /**
     * @var GuzzleClient
     */
    private $client;

    /**
     * Instantiates a new client.
     *
     * @param string   $apiKey
     * @param bool     $sandbox when true uses the sandbox API endpoint
     * @param string   $ssoKey  Single Sign-On key if generating sign in links
     * @param callable $handler optional Guzzle handler
     */
    public function __construct($apiKey, $sandbox = false, $ssoKey = false, $handler = null)
    {
        if (empty($apiKey)) {
            throw new InvalidArgumentException('You must provide an API Key');
        }

        // add any custom Guzzle HTTP handlers (useful for mocking)
        $handlerStack = ($handler) ? HandlerStack::create($handler) : HandlerStack::create();

        $this->apiKey = $apiKey;
        $this->sandbox = $sandbox;
        $this->ssoKey = $ssoKey;
        $this->caBundleFile = dirname(__DIR__).'/data/ca-bundle.crt';

        $this->client = new GuzzleClient([
            'base_uri' => $this->endpoint(),
            'handler' => $handlerStack,
        ]);

        // object endpoints
        $this->CatalogItem = new CatalogItem($this);
        $this->CreditNote = new CreditNote($this);
        $this->Customer = new Customer($this);
        $this->Estimate = new Estimate($this);
        $this->Event = new Event($this);
        $this->File = new File($this);
        $this->Invoice = new Invoice($this);
        $this->Plan = new Plan($this);
        $this->Subscription = new Subscription($this);
        $this->Transaction = new Transaction($this);
    }

    /**
     * Gets the API key used by this client.
     *
     * @return string
     */
    public function apiKey()
    {
        return $this->apiKey;
    }

    /**
     * Gets the API endpoint used by this client.
     *
     * @return string
     */
    public function endpoint()
    {
        return $this->sandbox ? self::API_BASE_SANDBOX : self::API_BASE;
    }

    /**
     * Performs an API request.
     *
     * @param string $method
     * @param string $endpoint
     * @param array  $params
     *
     * @throws Error\ErrorBase when the API request is not successful for any reason
     *
     * @return array
     */
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
            'verify' => $this->caBundleFile,
            'connect_timeout' => self::CONNECT_TIMEOUT,
            'read_timeout' => self::READ_TIMEOUT,
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
            throw new Error\ApiConnectionError('There was an error connecting to the Invoiced API. Please check your internet connection or status.invoiced.com for service outages. The reason was: '.$e->getMessage());
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
            } elseif ($code == 429) {
                throw new Error\RateLimitError($error['message'], $code, $error);
            } elseif ($code == 500) {
                throw new Error\ApiError($error['message'], $code, $error);
            } else {
                throw $this->generalApiError($code, $body);
            }
        }

        return $response;
    }

    /**
     * Throws a generic API error.
     *
     * @param int    $code
     * @param string $body
     *
     * @return Error\ApiError
     */
    private function generalApiError($code, $body)
    {
        return new Error\ApiError("API Error $code - $body", $code);
    }

    /**
     * Generates a single sign-on token for a customer.
     *
     * @param int $customerId customer ID on Invoiced
     * @param int $ttl        seconds until the login token expires
     *
     * @throws InvalidArgumentException when the token cannot be jenerated
     *
     * @return string
     */
    public function generateSignInToken($customerId, $ttl)
    {
        if (!$this->ssoKey) {
            throw new InvalidArgumentException('Please provide a single sign-on key! You can find this value in Settings > Developers > Single Sign-On of the Invoiced application.');
        }

        $params = [
            'iss' => 'Invoiced PHP/'.self::VERSION,
            'sub' => $customerId,
            'iat' => time(),
            'exp' => time() + $ttl,
        ];

        return JWT::encode($params, $this->ssoKey);
    }
}
