<?php

namespace Invoiced;

/**
 * Class Attachment
 * @package Invoiced
 * @property int $created_at
 * @property int $updated_at
 * @property array $file
 */
class Attachment extends BaseObject
{
    protected $_endpoint = '/attachments';
}
