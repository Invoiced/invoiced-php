<?php

namespace Invoiced;

/**
 * Class Email.
 *
 * @property int         $created_at
 * @property string      $email
 * @property string      $message
 * @property int         $opens
 * @property array       $opens_detail
 * @property string|null $reject_reason
 * @property string      $state
 * @property string      $subject
 * @property string      $template
 * @property int         $updated_at
 */
class Email extends BaseObject
{
    protected $_endpoint = '/emails';
}
