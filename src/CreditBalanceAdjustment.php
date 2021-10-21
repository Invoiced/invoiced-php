<?php

namespace Invoiced;

/**
 * @property int         $date
 * @property int         $id
 * @property int         $customer
 * @property string      $currency
 * @property float       $amount
 * @property string|null $notes
 * @property int         $created_at
 * @property int         $updated_at
 */
class CreditBalanceAdjustment extends BaseObject
{
    use Operations\Create;
    use Operations\All;
    use Operations\Update;
    use Operations\Delete;

    protected $_endpoint = '/credit_balance_adjustments';
}
