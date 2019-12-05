<?php

namespace Invoiced;

use BadMethodCallException;

class BankAccount extends PaymentSource
{
    use Operations\Delete;

    // phpunit considers closing braces to be executable,
    // so these functions will never show as covered.
    // They are tested in PaymentSourceTest, however.

    // @codeCoverageIgnoreStart

    /**
     * Overrides parent class function to throw exception.
     *
     * @param array $params
     * @param array $opts
     *
     * @throws BadMethodCallException
     */
    public function create(array $params = [], array $opts = [])
    {
        throw new BadMethodCallException('BankAccount does not support create(). Please use PaymentSource class.');
    }

    /**
     * Overrides parent class function to throw exception.
     *
     * @param array $opts
     *
     * @throws BadMethodCallException
     */
    public function all(array $opts = [])
    {
        throw new BadMethodCallException('BankAccount does not support all(). Please use PaymentSource class.');
    }

    // @codeCoverageIgnoreEnd
}
