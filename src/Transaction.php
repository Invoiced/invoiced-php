<?php

namespace Invoiced;

/**
 * @deprecated
 *
 * @property float  $amount
 * @property string $currency
 * @property int    $customer
 * @property string $status
 */
class Transaction extends BaseObject
{
    use Operations\Create;
    use Operations\All;
    use Operations\Update;
    use Operations\Delete;

    protected $_endpoint = '/transactions';

    /**
     * Sends a payment receipt.
     *
     * @param array<mixed> $params
     * @param array<mixed> $opts
     *
     * @return Email[]
     */
    public function send(array $params = [], array $opts = [])
    {
        $response = $this->_client->request('post', $this->getEndpoint().'/emails', $params, $opts);

        // build email objects
        $email = new Email($this->_client);

        return Util::buildObjects($email, $response['body']); /* @phpstan-ignore-line */
    }

    /**
     * Refunds this transaction.
     *
     * @param array<mixed> $params
     * @param array<mixed> $opts
     *
     * @return self
     */
    public function refund(array $params = [], array $opts = [])
    {
        $response = $this->_client->request('post', $this->getEndpoint().'/refunds', $params, $opts);

        return Util::convertToObject($this, $response['body']); /* @phpstan-ignore-line */
    }

    /**
     * Initiates a charge.
     *
     * @param array<mixed> $params
     * @param array<mixed> $opts
     *
     * @return self
     */
    public function initiateCharge(array $params = [], array $opts = [])
    {
        $response = $this->_client->request('post', '/charges', $params, $opts);

        return Util::convertToObject($this, $response['body']); /* @phpstan-ignore-line */
    }
}
