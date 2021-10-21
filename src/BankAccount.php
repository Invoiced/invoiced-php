<?php

namespace Invoiced;

/**
 * Class BankAccount.
 *
 * @property string $bank_name
 * @property string $currency
 * @property int    $routing_number
 * @property bool   $verified
 */
class BankAccount extends BasePaymentSourceObject
{
    protected $_endpoint = '/bank_accounts';
}
