<?php

namespace Invoiced;

class Transaction extends BaseObject
{
    use Operations\Create;
    use Operations\All;
    use Operations\Update;
    use Operations\Delete;

    /**
     * Sends a payment receipt.
     *
     * @param array $params
     * @param array $opts
     *
     * @return array(Invoiced\Transaction)
     */
    public function send(array $params = [], array $opts = [])
    {
        $response = $this->_client->request('post', $this->getEndpoint().'/emails', $params, $opts);

        // build email objects
        $email = new Email($this->_client);

        return Util::buildObjects($email, $response['body']);
    }

    /**
     * Refunds this transaction.
     *
     * @param array $params
     * @param array $opts
     *
     * @return self
     */
    public function refund(array $params = [], array $opts = [])
    {
        $response = $this->_client->request('post', $this->getEndpoint().'/refunds', $params, $opts);

        return Util::convertToObject($this, $response['body']);
    }
}
