<?php

namespace Invoiced;

/**
 * Class BasePaymentSourceObject
 * @package Invoiced
 * @property int $last4
 * @property int $updated_at
 */
class BasePaymentSourceObject extends BaseObject
{
    use Operations\Delete;
}
