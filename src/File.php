<?php

namespace Invoiced;

/**
 * @property string $name
 * @property int    $size
 * @property string $type
 * @property string $url
 */
class File extends BaseObject
{
    use Operations\Create;
    use Operations\Delete;

    protected $_endpoint = '/files';
}
