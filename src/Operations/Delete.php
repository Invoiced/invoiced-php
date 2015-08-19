<?php

namespace Invoiced\Operations;

trait Delete
{
	function delete()
	{
		$response = $this->_client->request('delete', $this->_endpoint);

		if ($response['code'] == 204) {
			$this->_values = ['id' => $this->id];
		}

		return $response['code'] == 204;
	}
}