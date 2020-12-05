<?php

namespace Invoiced;

/**
 * @property float  $amount
 * @property string $currency
 * @property int    $customer
 * @property string $status
 */
class Refund extends BaseObject
{
    /**
     * Creates an object.
     *
     * @param int          $chargeId
     * @param array<mixed> $params
     * @param array<mixed> $opts
     *
     * @return static
     */
    public function create($chargeId, array $params = [], array $opts = [])
    {
        $response = $this->_client->request('post', $this->getEndpoint().'/charges/'.$chargeId.'/refunds', $params, $opts);

        return Util::convertToObject($this, $response['body']); /* @phpstan-ignore-line */
    }
}
