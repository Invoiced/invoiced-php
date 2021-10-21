<?php

namespace Invoiced;

/**
 * @property float       $amount
 * @property string|null $catalog_item
 * @property int         $created_at
 * @property string      $currency
 * @property string      $interval
 * @property int         $interval_count
 * @property object      $metadata
 * @property string      $name
 * @property string      $pricing_mode
 * @property string      $quantity_type
 * @property array|null  $tiers
 * @property int         $updated_at
 */
class Plan extends BaseObject
{
    use Operations\Create;
    use Operations\All;
    use Operations\Update;
    use Operations\Delete;

    protected $_endpoint = '/plans';
}
