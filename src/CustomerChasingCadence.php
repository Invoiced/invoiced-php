<?php

namespace Invoiced;

class CustomerChasingCadence extends BaseObject
{
    use Operations\Create;
    use Operations\All;
    use Operations\Update;
    use Operations\Delete;

    protected $_endpoint = '/customer_chasing_cadences';
}
