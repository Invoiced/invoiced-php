<?php

namespace Invoiced;

use BadMethodCallException;

/**
 * @property object $approval
 * @property int    $created_at
 * @property array  $installments
 * @property string $status
 * @property int    $updated_at
 */
class PaymentPlan extends BaseObject
{
    use Operations\Delete;

    protected $_endpoint = '/payment_plans';

    /**
     * @param string|int|null $id
     * @param array<mixed>    $values
     */
    public function __construct(Client $client, $id = null, array $values = [])
    {
        parent::__construct($client, $id, $values);

        $this->_endpoint = '/payment_plan';
    }

    /**
     * Creates a payment plan.
     *
     * @param array<mixed> $params
     * @param array<mixed> $opts
     *
     * @return self newly created object
     */
    public function create(array $params = [], array $opts = [])
    {
        $response = $this->_client->request('put', $this->getEndpoint(), $params, $opts);

        return Util::convertToObject($this, $response['body']); /* @phpstan-ignore-line */
    }

    public function retrieve($id, array $opts = [])
    {
        throw new BadMethodCallException('PaymentPlan does not support retrieve(). Please use get() instead.');
    }

    /**
     * Retrieves a payment plan.
     *
     * @param array<mixed> $opts optional options to pass on
     *
     * @return PaymentPlan
     */
    public function get(array $opts = [])
    {
        $response = $this->_client->request('get', $this->getEndpoint(), $opts);

        return Util::convertToObject($this, $response['body']); /* @phpstan-ignore-line */
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
