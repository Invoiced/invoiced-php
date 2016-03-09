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
     * @return object newly created object
     */
    public function create(array $params = [])
    {
        $response = $this->_client->request('post', $this->getEndpoint(), $params);

        return Util::convertToObject($this, $response['body']);
    }
}
