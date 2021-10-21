<?php

namespace Invoiced;

/**
 * @property int         $amount
 * @property string      $catalog_item
 * @property int         $customer
 * @property string|null $description
 * @property bool        $discountable
 * @property array       $discounts
 * @property object      $metadata
 * @property string      $name
 * @property int         $quantity
 * @property true        $taxable
 * @property array       $taxes
 * @property string      $type
 * @property float       $unit_cost
 * @property int         $updated_at
 */
class LineItem extends BaseObject
{
    use Operations\Create;
    use Operations\All;
    use Operations\Update;
    use Operations\Delete;

    protected $_endpoint = '/line_items';
}
