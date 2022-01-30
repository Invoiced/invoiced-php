<?php

namespace Invoiced;

class EmailTemplate extends BaseObject
{
    use Operations\Create;
    use Operations\All;
    use Operations\Update;
    use Operations\Delete;

    protected $_endpoint = '/email_templates';
}
