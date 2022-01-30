<?php

namespace Invoiced;

class InvoiceChasingCadence extends BaseObject
{
    use Operations\Create;
    use Operations\All;
    use Operations\Update;
    use Operations\Delete;

    protected $_endpoint = '/invoice_chasing_cadences';
}
