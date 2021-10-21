<?php

namespace Invoiced;

/**
 * Class TextMessage.
 *
 * @property int    $created_at
 * @property string $message
 * @property string $state
 * @property string $to
 * @property int    $updated_at
 */
class TextMessage extends BaseObject
{
    protected $_endpoint = '/text_messages';
}
