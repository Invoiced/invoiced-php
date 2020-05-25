<?php

namespace Invoiced;

class Event extends BaseObject
{
    use Operations\All;

    protected $_endpoint = '/events';
}
