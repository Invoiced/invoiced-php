<?php

namespace Invoiced;

/**
 * @property string      $name
 * @property string      $number
 * @property string|null $email
 * @property string|null $phone
 * @property string|null $currency
 * @property string      $notes
 */
class Customer extends BaseObject
{
    use Operations\Create;
    use Operations\All;
    use Operations\Update;
    use Operations\Delete;

    protected $_endpoint = '/customers';

    /**
     * Sends a PDF statement to the customer.
     *
     * @param array<mixed> $params
     * @param array<mixed> $opts
     *
     * @return Email[]
     */
    public function sendStatement(array $params = [], array $opts = [])
    {
        $response = $this->_client->request('post', $this->getEndpoint().'/emails', $params, $opts);

        // build email objects
        $email = new Email($this->_client);

        return Util::buildObjects($email, $response['body']); /* @phpstan-ignore-line */
    }

    /**
     * Sends a PDF statement to the customer by SMS.
     *
     * @param array<mixed> $params
     * @param array<mixed> $opts
     *
     * @return TextMessage[]
     */
    public function sendStatementSMS(array $params = [], array $opts = [])
    {
        $response = $this->_client->request('post', $this->getEndpoint().'/text_messages', $params, $opts);

        // build text message objects
        $textMessage = new TextMessage($this->_client);

        return Util::buildObjects($textMessage, $response['body']); /* @phpstan-ignore-line */
    }

    /**
     * Sends a statement to the customer by mail.
     *
     * @param array<mixed> $params
     * @param array<mixed> $opts
     *
     * @return Letter[]
     */
    public function sendStatementLetter(array $params = [], array $opts = [])
    {
        $response = $this->_client->request('post', $this->getEndpoint().'/letters', $params, $opts);

        // build letter objects
        $letter = new Letter($this->_client);

        return Util::buildObjects($letter, $response['body']); /* @phpstan-ignore-line */
    }

    /**
     * Gets the customer's current balance.
     *
     * @return object balance
     */
    public function balance()
    {
        $response = $this->_client->request('get', $this->getEndpoint().'/balance');

        // we actually want an object instead of an array...
        return json_decode(json_encode($response['body']), false); /* @phpstan-ignore-line */
    }

    /**
     * Gets a contact object for this customer.
     *
     * @return Contact
     */
    public function contacts()
    {
        $contact = new Contact($this->_client);

        return $contact->setEndpointBase($this->getEndpoint());
    }

    /**
     * Gets a note object for this customer.
     *
     * @return Note
     */
    public function notes()
    {
        $note = new Note($this->_client);

        return $note->setEndpointBase($this->getEndpoint());
    }

    /**
     * Gets a line item object for this customer.
     *
     * @return LineItem
     */
    public function lineItems()
    {
        $line = new LineItem($this->_client);

        return $line->setEndpointBase($this->getEndpoint());
    }

    /**
     * Gets a payment source object for this customer.
     *
     * @return PaymentSource
     */
    public function paymentSources()
    {
        $source = new PaymentSource($this->_client);

        return $source->setEndpointBase($this->getEndpoint());
    }

    /**
     * Creates an invoice from pending line items.
     *
     * @param array<mixed> $params
     * @param array<mixed> $opts
     *
     * @return Invoice
     */
    public function invoice(array $params = [], array $opts = [])
    {
        $response = $this->_client->request('post', $this->getEndpoint().'/invoices', $params, $opts);

        // build invoice object
        $invoice = new Invoice($this->_client);

        return Util::convertToObject($invoice, $response['body']); /* @phpstan-ignore-line */
    }

    /**
     * Creates a consolidated invoice for this customer.
     *
     * @param array<mixed> $params
     *
     * @return Invoice
     */
    public function consolidateInvoices(array $params = [])
    {
        $response = $this->_client->request('post', $this->getEndpoint().'/consolidate_invoices', $params);

        // build invoice object
        $invoice = new Invoice($this->_client);

        return Util::convertToObject($invoice, $response['body']); /* @phpstan-ignore-line */
    }
}
