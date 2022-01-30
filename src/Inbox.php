<?php

namespace Invoiced;

class Inbox extends BaseObject
{
    use Operations\All;

    protected $_endpoint = '/inboxes';
}
