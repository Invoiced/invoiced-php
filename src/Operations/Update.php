<?php

namespace Invoiced\Operations;

trait Update
{
	function save(array $params = [])
	{
		$update = [];

		foreach ($this->_unsaved as $k) {
			$update[$k] = $this->_values[$k];
		}

		$update = array_replace($update, $params);

		# perform the update if there are any changes
		if (count($update) > 0) {
			$response = $this->_client->request('patch', $this->_endpoint, $update);

			# update the local values with the response
			$this->_values = array_replace((array) $response['body'], ['id' => $this->id]);
			$this->_unsaved = [];

			return $response['code'] == 200;
		}

		return false;
	}
}