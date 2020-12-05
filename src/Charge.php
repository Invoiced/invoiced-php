<?php

namespace Invoiced;

/**
 * @property float  $amount
 * @property string $currency
 * @property int    $customer
 * @property string $status
 */
class Charge extends BaseObject
{
    use Operations\Create;

    protected $_endpoint = '/charges';
}
