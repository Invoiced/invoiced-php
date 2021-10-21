<?php

namespace Invoiced;

/**
 * Class Card.
 *
 * @property string $brand
 * @property int    $exp_month
 * @property int    $exp_year
 * @property string $funding
 */
class Card extends BasePaymentSourceObject
{
    protected $_endpoint = '/cards';
}
