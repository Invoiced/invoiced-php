<?php

namespace Invoiced;

class Invoice extends BaseObject
{
    use Operations\Create;
    use Operations\All;
    use Operations\Update;
    use Operations\Delete;

    /**
     * Sends the invoice to the customer.
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
     * Sends the invoice to the customer by SMS.
     *
     * @param array $params
     * @param array $opts
     *
     * @return array(Invoiced\TextMessage)
     */
    public function sendSMS(array $params = [], array $opts = [])
    {
        $response = $this->_client->request('post', $this->getEndpoint().'/text_messages', $params, $opts);

        // build text message objects
        $textMessage = new TextMessage($this->_client);

        return Util::buildObjects($textMessage, $response['body']);
    }

    /**
     * Sends the invoice to the customer by mail.
     *
     * @param array $params
     * @param array $opts
     *
     * @return array(Invoiced\Letter)
     */
    public function sendLetter(array $params = [], array $opts = [])
    {
        $response = $this->_client->request('post', $this->getEndpoint().'/letters', $params, $opts);

        // build letter objects
        $letter = new Letter($this->_client);

        return Util::buildObjects($letter, $response['body']);
    }

    /**
     * Attempts to collect payment on the invoice.
     *
     * @param array $opts
     *
     * @return bool
     */
    public function pay(array $opts = [])
    {
        $response = $this->_client->request('post', $this->getEndpoint().'/pay', [], $opts);

        // update the local values with the response
        $this->_values = array_replace((array) $response['body'], ['id' => $this->id]);
        $this->_unsaved = [];

        return $response['code'] == 200;
    }

    /**
     * Fetches the invoice's file attachments.
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
     * Gets a payment plan object for this invoice.
     *
     * @return PaymentPlan
     */
    public function paymentPlan()
    {
        $paymentPlan = new PaymentPlan($this->_client);
        $paymentPlan->setEndpointBase($this->getEndpoint());

        return $paymentPlan;
    }

    /**
     * Gets a note object for this invoice.
     *
     * @return Note
     */
    public function notes()
    {
        $note = new Note($this->_client);
        $note->setEndpointBase($this->getEndpoint());

        return $note;
    }

    /**
     * Voids the invoice.
     *
     * @return Invoice
     */
    public function void()
    {
        $response = $this->_client->request('post', $this->getEndpoint().'/void', [], []);

        // update the local values with the response
        $this->_values = array_replace((array) $response['body'], ['id' => $this->id]);

        return $response['code'] == 200;
    }
}
