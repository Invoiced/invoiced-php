<?php

namespace Invoiced;

/**
 * Class Invoice.
 * Class Invoice.
 *
 * @property int         $attempt_count
 * @property bool        $autopay
 * @property float       $balance
 * @property null|int    $due_date
 * @property null|int    $next_payment_attempt
 * @property bool        $paid
 * @property null|int    $payment_plan
 * @property null|string $payment_terms
 * @property string      $payment_url
 * @property null|object $ship_to
 * @property null|int    $subscription
 */
class Invoice extends BaseDocument
{
    protected $_endpoint = '/invoices';

    /**
     * Sends the invoice to the customer.
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
     * Sends the invoice to the customer by SMS.
     *
     * @param array<mixed> $params
     * @param array<mixed> $opts
     *
     * @return TextMessage[]
     */
    public function sendSMS(array $params = [], array $opts = [])
    {
        $response = $this->_client->request('post', $this->getEndpoint().'/text_messages', $params, $opts);

        // build text message objects
        $textMessage = new TextMessage($this->_client);

        return Util::buildObjects($textMessage, $response['body']); /* @phpstan-ignore-line */
    }

    /**
     * Sends the invoice to the customer by mail.
     *
     * @param array<mixed> $params
     * @param array<mixed> $opts
     *
     * @return Letter[]
     */
    public function sendLetter(array $params = [], array $opts = [])
    {
        $response = $this->_client->request('post', $this->getEndpoint().'/letters', $params, $opts);

        // build letter objects
        $letter = new Letter($this->_client);

        return Util::buildObjects($letter, $response['body']); /* @phpstan-ignore-line */
    }

    /**
     * Attempts to collect payment on the invoice.
     *
     * @param array<mixed> $opts
     *
     * @return bool
     */
    public function pay(array $opts = [])
    {
        $response = $this->_client->request('post', $this->getEndpoint().'/pay', [], $opts);

        // update the local values with the response
        $this->_values = array_replace((array) $response['body'], ['id' => $this->id]);
        $this->_unsaved = [];

        return 200 == $response['code'];
    }

    /**
     * Gets a payment plan object for this invoice.
     *
     * @return PaymentPlan
     */
    public function paymentPlan()
    {
        $paymentPlan = new PaymentPlan($this->_client);

        return $paymentPlan->setEndpointBase($this->getEndpoint());
    }

    /**
     * Gets a note object for this invoice.
     *
     * @return Note
     */
    public function notes()
    {
        $note = new Note($this->_client);

        return $note->setEndpointBase($this->getEndpoint());
    }
}
