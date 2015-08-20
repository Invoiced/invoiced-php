<?php

namespace Invoiced\Operations;

use Invoiced\Util;

trait Create
{
    /**
     * Creates an object.
     *
     * @param array $params
     *
     * @return Object newly created object
     */
    public function create(array $params = [])
    {
        $response = $this->_client->request('post', $this->_endpoint, $params);

        return Util::convertToObject($this, $response['body']);
    }
}
