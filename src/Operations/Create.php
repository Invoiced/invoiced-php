<?php

namespace Invoiced\Operations;

use Invoiced\Util;

trait Create
{
    /**
     * Creates an object.
     *
     * @param array $params
     * @param array $opts
     *
     * @return object newly created object
     */
    public function create(array $params = [], array $opts = [])
    {
        $response = $this->_client->request('post', $this->getEndpoint(), $params, $opts);

        return Util::convertToObject($this, $response['body']);
    }
}
