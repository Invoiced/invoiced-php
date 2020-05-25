<?php

namespace Invoiced;

class LineItem extends BaseObject
{
    use Operations\Create;
    use Operations\All;
    use Operations\Update;
    use Operations\Delete;

    protected $_endpoint = '/line_items';
}
