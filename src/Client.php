<?php

namespace Invoiced;

use Exception;
use InvalidArgumentException;
use GuzzleHttp\Client as GuzzleClient;
use GuzzleHttp\Exception\RequestException;

class Client
{
	const API_BASE = 'https://api.invoiced.com';

	const VERSION = '0.0.1';

	var $Customer;
	var $Invoice;
	var $Transaction;

	/**
	 * @var string
	 */
	private $apiKey;

	/**
	 * @var GuzzleHttp\Client
	 */
	private $client;

	/**
	 * Instantiates a new client
	 *
	 * @param string $apiKey
	 */
	function __construct($apiKey)
	{
		if (empty($apiKey)) {
			throw new InvalidArgumentException('You must provide an API Key');
		}

		$this->apiKey = $apiKey;
		$this->client = new GuzzleClient(['base_uri' => self::API_BASE]);

		$this->Customer = new Customer($this);
		$this->Invoice = new Invoice($this);
		$this->Transaction = new Transaction($this);
	}

	function apiKey()
	{
		return $this->apiKey;
	}

	function request($method, $endpoint, $params = [])
	{
		$method = strtolower($method);

		$headers = [
			'Content-Type' => 'application/json',
			'User-Agent' => 'Invoiced PHP/'.self::VERSION
		];

		$query = ['envelope' => false];

		$request = [
			'auth' => [$this->apiKey, ''],
			'headers' => $headers,
			'http_errors' => false
		];

		// these methods have no request body
		if ($method === 'get' || $method === 'delete') {
			$query = array_replace($query, $params);
		} else {
			$request['json'] = $params;
		}

		$request['query'] = $query;

		try {
			// perform the request
			$response = $this->client->$method($endpoint, $request);
		} catch (RequestException $e) {
			throw new Error\ApiError('There was an error connecting to the Invoiced API.');
		}

		// validate response
		$code = $response->getStatusCode();
		$body = $response->getBody();

		if ($code >= 200 && $code < 400) {
			$parsed = null;

			// expect a JSON response unless we received 204 No Content
			if ($code != 204) {
				try {
					$parsed = json_decode($body, true);
				} catch (Exception $e) {
					throw $this->generalApiError($code, $body);
				}
			}

			$parsedHeaders = [];
			foreach ($response->getHeaders() as $k => $v) {
				$parsedHeaders[$k] = implode(', ', $v);
			}

			return [
				'code' => $code,
				'headers' => $parsedHeaders,
				'body' => $parsed
			];
		} else {
			try {
				$error = json_decode($body, true);
			} catch (Exception $e) {
				throw $this->generalApiError($code, $body);
			}

			if ($code == 401) {
				throw new Error\AuthenticationError($error['message'], $code, $error);
			} else if (in_array($code, [400, 403, 404])) {
				throw new Error\InvalidRequest($error['message'], $code, $error);
			} else if ($code == 500) {
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