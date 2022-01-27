<?php

namespace Invoiced\Operations;

use Invoiced\Collection;
use Invoiced\Util;

trait All
{
    /**
     * Fetches a collection of objects.
     *
     * @param array<mixed> $opts
     *
     * @return array<mixed> [array(Invoiced\Object), Invoiced\Collection]
     */
    public function all(array $opts = [])
    {
        $response = $this->_client->request('get', $this->getEndpoint(), $opts);

        // build objects
        $objects = Util::buildObjects($this, $response['body']);

        $metadata = [];

        if (isset($response['headers']['Link']) && isset($response['headers']['X-Total-Count'])) {
            // store the metadata from the list operation
            $metadata = new Collection($response['headers']['Link'], $response['headers']['X-Total-Count']);
        }

        return [$objects, $metadata];
    }
}
