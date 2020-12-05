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
     * Refunds a charge.
     *
     * @param int          $chargeId
     * @param array<mixed> $params
     * @param array<mixed> $opts
     *
     * @return Refund
     */
    public function refund($chargeId, array $params = [], array $opts = [])
    {
        $response = $this->_client->request('post', $this->getEndpoint().'/'.$chargeId.'/refunds', $params, $opts);
        $refund = new Refund($this->_client);

        return Util::convertToObject($refund, $response['body']); /* @phpstan-ignore-line */
    }
}
