<?php

namespace Invoiced;

class SmsTemplate extends BaseObject
{
    use Operations\Create;
    use Operations\All;
    use Operations\Update;
    use Operations\Delete;

    protected $_endpoint = '/sms_templates';
}
