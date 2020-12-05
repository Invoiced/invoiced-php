<?php

namespace Invoiced;

/**
 * @property int    $customer
 * @property string $notes
 */
class Note extends BaseObject
{
    use Operations\Create;
    use Operations\All;
    use Operations\Update;
    use Operations\Delete;

    protected $_endpoint = '/notes';
}
