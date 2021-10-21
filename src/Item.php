<?php

namespace Invoiced;

/**
 * @property string|null $avalara_location_code
 * @property string|null $avalara_tax_code
 * @property int         $created_at
 * @property string      $currency
 * @property string|null $description
 * @property bool        $discountable
 * @property string|null $gl_account
 * @property string      $id
 * @property object      $metadata
 * @property string      $name
 * @property bool        $taxable
 * @property array       $taxes
 * @property string      $type
 * @property float       $unit_cost
 * @property int         $updated_at
 */
class Item extends BaseObject
{
    use Operations\Create;
    use Operations\All;
    use Operations\Update;
    use Operations\Delete;

    protected $_endpoint = '/items';
}
