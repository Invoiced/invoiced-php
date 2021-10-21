<?php

namespace Invoiced;

/**
 * Class Letter.
 *
 * @property int    $created_at
 * @property int    $expected_delivery_date
 * @property string $id
 * @property int    $num_pages
 * @property string $state
 * @property string $to
 * @property int    $updated_at
 */
class Letter extends BaseObject
{
    protected $_endpoint = '/letters';
}
