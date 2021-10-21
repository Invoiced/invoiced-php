<?php

namespace Invoiced;

/**
 * Class CreditNote.
 *
 * @property float $balance
 * @property bool  $paid
 */
class CreditNote extends BaseDocument
{
    protected $_endpoint = '/credit_notes';

    /**
     * Sends the credit note to the customer,.
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
