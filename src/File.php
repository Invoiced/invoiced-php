<?php

namespace Invoiced;

class File extends BaseObject
{
    use Operations\Create;
    use Operations\Delete;

    protected $_endpoint = '/files';
}
