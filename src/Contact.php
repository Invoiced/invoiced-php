<?php

namespace Invoiced;

/**
 * @property string      $name
 * @property string|null $email
 * @property string|null $phone
 */
class Contact extends BaseObject
{
    use Operations\Create;
    use Operations\All;
    use Operations\Update;
    use Operations\Delete;

    protected $_endpoint = '/contacts';
}
