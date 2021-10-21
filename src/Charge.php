<?php

namespace Invoiced;

/**
 * @property float       $amount
 * @property float       $amount_refunded
 * @property int         $created_at
 * @property string      $currency
 * @property int         $customer
 * @property bool        $disputed
 * @property null|string $failure_message
 * @property string      $gateway
 * @property string      $gateway_id
 * @property object      $payment_source
 * @property null|string $receipt_email
 * @property bool        $refunded
 * @property array       $refunds
 * @property string      $status
 * @property int         $updated_at
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
