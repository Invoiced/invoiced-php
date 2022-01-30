<?php

namespace Invoiced;

class MerchantAccount extends BaseObject
{
    use Operations\Create;
    use Operations\All;
    use Operations\Update;
    use Operations\Delete;

    protected $_endpoint = '/merchant_accounts';
}
