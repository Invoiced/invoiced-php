<?php

namespace Invoiced;

/**
 * @property float  $amount
 * @property string $currency
 * @property int    $customer
 * @property string $status
 */
class Charge extends BaseObject
{
    use Operations\Create;

    protected $_endpoint = '/charges';

    /**
     * Creates an object.
     *
     * @param array<mixed> $params
     * @param array<mixed> $opts
     *
     * @return Payment newly created object
     */
    public function create(array $params = [], array $opts = [])
    {
        $response = $this->_client->request('post', $this->getEndpoint(), $params, $opts);
        $payment = new Payment($this->_client);

        return Util::convertToObject($payment, $response['body']); /* @phpstan-ignore-line */
    }
}
