<?php

namespace Invoiced;

class Transaction extends Object
{
    use Operations\Create;
    use Operations\All;
    use Operations\Update;
    use Operations\Delete;

    /**
     * Sends a payment receipt.
     *
     * @param array $opts
     *
     * @return array(Invoiced\Transaction)
     */
    public function send(array $opts = [])
    {
        $response = $this->_client->request('post', $this->getEndpoint().'/emails', $opts);

        // build email objects
        $email = new Email($this->_client);

        return Util::buildObjects($email, $response['body']);
    }

    /**
     * Refunds this transaction.
     *
     * @param array $opts
     *
     * @return Invoiced\Transaction
     */
    public function refund(array $opts = [])
    {
        $response = $this->_client->request('post', $this->getEndpoint().'/refunds', $opts);

        return Util::convertToObject($this, $response['body']);
    }
}
