<?php

namespace Invoiced;

class BankAccount extends BaseObject
{
    use Operations\Delete;

    /**
     * Creates an object. This variant adjusts the endpoint for this operation only.
     *
     * @param array $params
     * @param array $opts
     *
     * @return object newly created object
     */
    public function create(array $params = [], array $opts = [])
    {
        $response = $this->_client->request('post', $this->getEndpointBase() . "/payment_sources", $params, $opts);

        return Util::convertToObject($this, $response['body']);
    }
}