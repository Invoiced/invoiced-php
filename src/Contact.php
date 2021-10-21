<?php

namespace Invoiced;

/**
 * @property string|null $address1
 * @property string|null $address2
 * @property string|null $city
 * @property string|null $country
 * @property int         $created_at
 * @property string|null $department
 * @property string|null $email
 * @property string      $name
 * @property string|null $phone
 * @property string|null $postal_code
 * @property bool        $primary
 * @property null|bool   $sms_enabled
 * @property string|null $state
 * @property string|null $title
 * @property int         $updated_at
 */
class Contact extends BaseObject
{
    use Operations\Create;
    use Operations\All;
    use Operations\Update;
    use Operations\Delete;

    protected $_endpoint = '/contacts';
}
