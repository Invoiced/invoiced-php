<?php

namespace Invoiced;

class Estimate extends BaseDocument
{
    protected $_endpoint = '/estimates';

    /**
     * Sends the estimate to the customer,.
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
     * Creates an invoice from this estimate.
     *
     * @param array<mixed> $params
     * @param array<mixed> $opts
     *
     * @return Invoice
     */
    public function invoice(array $params = [], array $opts = [])
    {
        $response = $this->_client->request('post', $this->getEndpoint().'/invoice', $params, $opts);

        // build invoice object
        $invoice = new Invoice($this->_client);

        return Util::convertToObject($invoice, $response['body']); /* @phpstan-ignore-line */
    }
}
