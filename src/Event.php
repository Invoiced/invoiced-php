<?php

namespace Invoiced;

/**
 * Class Event.
 *
 * @property object $data
 * @property string $type
 * @property int    $timestamp
 */
class Event extends BaseObject
{
    use Operations\All;

    protected $_endpoint = '/events';
}
