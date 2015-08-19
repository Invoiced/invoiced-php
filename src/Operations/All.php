<?php

namespace Invoiced\Operations;

use Invoiced\Collection;
use Invoiced\Util;

trait All
{
	function all(array $opts = [])
	{
		$response = $this->_client->request('get', $this->_endpoint, $opts);

		# build objects
		$objects = Util::buildObjects($this, $response['body']);

		# store the metadata from the list operation
		$metadata = new Collection($response['headers']['Link'], $response['headers']['X-Total-Count']);

		return [$objects, $metadata];
	}
}