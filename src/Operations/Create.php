<?php

namespace Invoiced\Operations;

use Invoiced\Error\ErrorBase;
use Invoiced\Util;

trait Create
{
    /**
     * Creates an object.
     *
     * @param array<mixed> $params
     * @param array<mixed> $opts
     *
     * @throws ErrorBase
     *
     * @return static newly created object
     */
    public function create(array $params = [], array $opts = [])
    {
        $response = $this->_client->request('post', $this->getEndpoint(), $params, $opts);

        return Util::convertToObject($this, $response['body']); /* @phpstan-ignore-line */
    }
}
