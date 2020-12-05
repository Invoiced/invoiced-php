<?php

namespace Invoiced;

/**
 * @property string      $name
 * @property string|null $description
 * @property float       $amount
 * @property float       $unit_cost
 */
class LineItem extends BaseObject
{
    use Operations\Create;
    use Operations\All;
    use Operations\Update;
    use Operations\Delete;

    protected $_endpoint = '/line_items';
}
