<?php

namespace Invoiced;

/**
 * @property int      $created_at
 * @property int      $customer
 * @property int|null $invoice
 * @property string   $notes
 * @property object   $user
 * @property int      $updated_at
 *                                }
 */
class Note extends BaseObject
{
    use Operations\Create;
    use Operations\All;
    use Operations\Update;
    use Operations\Delete;

    protected $_endpoint = '/notes';
}
