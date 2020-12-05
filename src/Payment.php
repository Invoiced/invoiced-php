<?php

namespace Invoiced;

/**
 * @property float    $amount
 * @property string   $currency
 * @property int|null $customer
 * @property string   $status
 */
class Payment extends BaseObject
{
    use Operations\Create;
    use Operations\All;
    use Operations\Update;
    use Operations\Delete;

    protected $_endpoint = '/payments';

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
}
