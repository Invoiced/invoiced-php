<?php

namespace Invoiced;

/**
 * Class PaymentSource.
 *
 * @property string $brand
 * @property int    $exp_month
 * @property int    $exp_year
 * @property string $funding
 * @property int    $last
 * @property int    $updated_at
 */
class PaymentSource extends BaseObject
{
    use Operations\Create;
    use Operations\All;

    protected $_endpoint = '/payment_sources';
}
