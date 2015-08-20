<?php

namespace Invoiced;

class Invoice extends Object
{
	use Operations\Create;
	use Operations\All;
	use Operations\Update;
	use Operations\Delete;

	function send(array $opts = [])
	{
		$response = $this->_client->request('post', $this->_endpoint.'/emails', $opts);

		// build email objects
		$email = new Email($this->_client);
		return Util::buildObjects($email, $response['body']);
	}

	function pay()
	{
		$response = $this->_client->request('post', $this->_endpoint.'/pay');

		// update the local values with the response
		$this->_values = array_replace((array) $response['body'], ['id' => $this->id]);
		$this->_unsaved = [];

		return $response['code'] == 200;
	}
}