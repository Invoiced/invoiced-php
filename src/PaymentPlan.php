<?php

namespace Invoiced;

use BadMethodCallException;

class PaymentPlan extends Object
{
    use Operations\Delete;

    public function __construct(Client $client, $id = null, array $values = [])
    {
        parent::__construct($client, $id, $values);

        $this->_endpoint = '/payment_plan';
    }

    /**
     * Creates a payment plan.
     *
     * @param array $params
     * @param array $opts
     *
     * @return object newly created object
     */
    public function create(array $params = [], array $opts = [])
    {
        $response = $this->_client->request('put', $this->getEndpoint(), $params, $opts);

        return Util::convertToObject($this, $response['body']);
    }

    public function retrieve($id, array $opts = [])
    {
        throw new BadMethodCallException('PaymentPlan does not support retrieve(). Please use get() instead.');
    }

    /**
     * Retrieves a payment plan.
     *
     * @param array $opts optional options to pass on
     *
     * @return PaymentPlan
     */
    public function get(array $opts = [])
    {
        $response = $this->_client->request('get', $this->getEndpoint(), $opts);

        return Util::convertToObject($this, $response['body']);
    }

    /**
     * Cancels the payment plan.
     *
     * @return bool
     */
    public function cancel()
    {
        return $this->delete();
    }
}
