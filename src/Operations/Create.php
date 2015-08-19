<?php

namespace Invoiced\Operations;

use Invoiced\Util;

trait Create
{
	function create(array $params = [])
	{
		$response = $this->_client->request('post', $this->_endpoint, $params);

		return Util::convertToObject($this, $response['body']);
	}
}