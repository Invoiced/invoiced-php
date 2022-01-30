<?php

namespace Invoiced;

class PaymentMethod extends BaseObject
{
    use Operations\All;
    use Operations\Update;

    protected $_endpoint = '/payment_methods';
}
