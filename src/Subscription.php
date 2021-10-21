<?php

namespace Invoiced;

/**
 * @property array       $addons
 * @property string      $bill_in
 * @property int         $bill_in_advance_days
 * @property bool        $cancel_at_period_end
 * @property int|null    $canceled_at
 * @property int         $contract_period_end
 * @property int         $contract_period_start
 * @property int|null    $contract_renewal_cycles
 * @property string      $contract_renewal_mode
 * @property int         $created_at
 * @property int         $customer
 * @property int|null    $cycles
 * @property array       $discounts
 * @property object      $metadata
 * @property float       $mrr
 * @property string      $object
 * @property int         $paused
 * @property int|null    $period_end
 * @property int|null    $period_start
 * @property string      $plan
 * @property int         $quantity
 * @property float       $recurring_total
 * @property object|null $ship_to
 * @property int         $start_date
 * @property string      $status
 * @property array       $taxes
 * @property string      $url
 * @property int         $updated_at
 */
class Subscription extends BaseObject
{
    use Operations\Create;
    use Operations\All;
    use Operations\Update;
    use Operations\Delete;

    protected $_endpoint = '/subscriptions';

    /**
     * Cancels the subscription.
     *
     * @return bool
     */
    public function cancel()
    {
        return $this->delete();
    }

    /**
     * Previews the subscription.
     *
     * @param array<mixed> $params
     * @param array<mixed> $opts
     *
     * @return object
     */
    public function preview(array $params = [], array $opts = [])
    {
        $response = $this->_client->request('post', '/subscriptions/preview', $params, $opts);

        // return generic JSON (not actual subscription object)
        return json_decode(json_encode($response['body']), false); /* @phpstan-ignore-line */
    }
}
