<?php

namespace Invoiced;

/**
 * Class Task.
 *
 * @property string   $action
 * @property int|null $chase_step_id
 * @property bool     $complete
 * @property int|null $completed_by_user_id
 * @property int|null $completed_date
 * @property int      $created_at
 * @property int      $customer_id
 * @property int      $due_date
 * @property string   $name
 * @property int|null $user_id
 * @property int      $updated_at
 */
class Task extends BaseObject
{
    use Operations\Create;
    use Operations\All;
    use Operations\Update;
    use Operations\Delete;

    protected $_endpoint = '/tasks';
}
