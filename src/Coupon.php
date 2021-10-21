<?php

namespace Invoiced;

/**
 * @property string   $id
 * @property string   $object
 * @property string   $name
 * @property string   $currency
 * @property bool     $is_percent
 * @property bool     $exclusive
 * @property int|null $duration
 * @property int|null $expiration_date
 * @property int|null $max_redemptions
 * @property int      $created_at
 * @property object   $metadata
 * @property int      $updated_at
 */
class Coupon extends BaseObject
{
    use Operations\Create;
    use Operations\All;
    use Operations\Update;
    use Operations\Delete;

    protected $_endpoint = '/coupons';
}
