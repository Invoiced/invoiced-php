<?php

namespace Invoiced;


/**
 * Class Event
 * @package Invoiced
 * @property object $data
 * @property string $type
 * @property int $timestamp
 */
class Event extends BaseObject
{
    use Operations\All;

    protected $_endpoint = '/events';
}
