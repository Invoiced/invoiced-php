<?php

namespace Invoiced;

/**
 * Class BasePaymentSourceObject.
 *
 * @property int $last4
 * @property int $updated_at
 */
class BasePaymentSourceObject extends BaseObject
{
    use Operations\Delete;
}
