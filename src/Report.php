<?php

namespace Invoiced;

class Report extends BaseObject
{
    use Operations\Create;

    protected $_endpoint = '/reports';
}
