<?php

namespace Invoiced;

class Estimate extends BaseObject
{
    use Operations\Create;
    use Operations\All;
    use Operations\Update;
    use Operations\Delete;

    /*
     * Sends the estimate to the customer,
     *
     * @param array $params
     * @param array $opts
     *
     * @return array(Invoiced\Email)
     */
    public function send(array $params = [], array $opts = [])
    {
        $response = $this->_client->request('post', $this->getEndpoint().'/emails', $params, $opts);

        // build email objects
        $email = new Email($this->_client);

        return Util::buildObjects($email, $response['body']);
    }

    /**
     * Fetches the estimate's file attachments.
     *
     * @param array $opts
     *
     * @return [array(Invoiced\Object), Invoiced\Collection]
     */
    public function attachments(array $opts = [])
    {
        $response = $this->_client->request('get', $this->getEndpoint().'/attachments', $opts);

        // ensure each attachment has an ID
        $body = $response['body'];
        foreach ($body as &$obj) {
            if (!isset($obj['id'])) {
                $obj['id'] = $obj['file']['id'];
            }
        }

        // build attachment objects
        $attachment = new Attachment($this->_client);
        $attachments = Util::buildObjects($attachment, $body);

        // store the metadata from the list operation
        $metadata = new Collection($response['headers']['Link'], $response['headers']['X-Total-Count']);

        return [$attachments, $metadata];
    }

    /**
     * Creates an invoice from this estimate.
     *
     * @param array $params
     * @param array $opts
     *
     * @return Invoice
     */
    public function invoice(array $params = [], array $opts = [])
    {
        $response = $this->_client->request('post', $this->getEndpoint().'/invoice', $params, $opts);

        // build invoice object
        $invoice = new Invoice($this->_client);

        return Util::convertToObject($invoice, $response['body']);
    }
}
