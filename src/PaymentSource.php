<?php

namespace Invoiced;

class PaymentSource extends BaseObject
{
    use Operations\Create;
    use Operations\All;

    protected $_endpoint = '/payment_sources';
}
