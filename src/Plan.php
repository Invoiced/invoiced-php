<?php

namespace Invoiced;

/**
 * @property string      $name
 * @property string|null $currency
 * @property int|null    $amount
 */
class Plan extends BaseObject
{
    use Operations\Create;
    use Operations\All;
    use Operations\Update;
    use Operations\Delete;

    protected $_endpoint = '/plans';
}
