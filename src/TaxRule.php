<?php

namespace Invoiced;

class TaxRule extends BaseObject
{
    use Operations\Create;
    use Operations\All;
    use Operations\Update;
    use Operations\Delete;

    protected $_endpoint = '/tax_rules';
}