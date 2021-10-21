<?php

namespace Invoiced;

/**
 * @property float       $amount
 * @property int         $charge
 * @property int         $created_at
 * @property string      $currency
 * @property string|null $failure_message
 * @property string      $gateway
 * @property string      $gateway_id
 * @property string      $status
 * @property int         $updated_at
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
