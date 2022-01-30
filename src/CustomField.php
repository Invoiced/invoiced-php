<?php

namespace Invoiced;

class CustomField extends BaseObject
{
    use Operations\Create;
    use Operations\All;
    use Operations\Update;
    use Operations\Delete;

    protected $_endpoint = '/custom_fields';
}
