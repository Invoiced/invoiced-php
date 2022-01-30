<?php

namespace Invoiced;

class Webhook extends BaseObject
{
    use Operations\Create;
    use Operations\All;
    use Operations\Update;
    use Operations\Delete;

    protected $_endpoint = '/webhooks';
}
