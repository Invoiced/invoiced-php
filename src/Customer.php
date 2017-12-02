<?php

namespace Invoiced;

class Customer extends BaseObject
{
    use Operations\Create;
    use Operations\All;
    use Operations\Update;
    use Operations\Delete;

    /*
     * Sends a PDF statement to the customer
     *
     * @param array $params
     * @param array $opts
     *
     * @return array(Invoiced\Email)
     */
    public function sendStatement(array $params = [], array $opts = [])
    {
        $response = $this->_client->request('post', $this->getEndpoint().'/emails', $params, $opts);

        // build email objects
        $email = new Email($this->_client);

        return Util::buildObjects($email, $response['body']);
    }

    /**
     * Gets the customer's current balance.
     *
     * @return \stdClass balance
     */
    public function balance()
    {
        $response = $this->_client->request('get', $this->getEndpoint().'/balance');

        // we actually want an object instead of an array...
        return json_decode(json_encode($response['body']), false);
    }

    /**
     * Gets a contact object for this customer.
     *
     * @return Contact
     */
    public function contacts()
    {
        $line = new Contact($this->_client);
        $line->setEndpointBase($this->getEndpoint());

        return $line;
    }

    /**
     * Gets a line item object for this customer.
     *
     * @return LineItem
     */
    public function lineItems()
    {
        $line = new LineItem($this->_client);
        $line->setEndpointBase($this->getEndpoint());

        return $line;
    }

    /**
     * Creates an invoice from pending line items.
     *
     * @param array $params
     * @param array $opts
     *
     * @return Invoice
     */
    public function invoice(array $params = [], array $opts = [])
    {
        $response = $this->_client->request('post', $this->getEndpoint().'/invoices', $params, $opts);

        // build invoice object
        $invoice = new Invoice($this->_client);

        return Util::convertToObject($invoice, $response['body']);
    }
}
