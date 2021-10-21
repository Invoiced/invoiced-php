<?php

namespace Invoiced;

/**
 * @property string $id
 * @property string $object
 * @property string $name
 * @property string $currency
 * @property bool   $is_percent
 * @property bool   $inclusive
 * @property int    $created_at
 * @property object $metadata
 * @property int    $updated_at
 */
class TaxRate extends BaseObject
{
    use Operations\Create;
    use Operations\All;
    use Operations\Update;
    use Operations\Delete;

    protected $_endpoint = '/tax_rates';
}
