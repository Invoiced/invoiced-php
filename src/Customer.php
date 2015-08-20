<?php

namespace Invoiced;

class Customer extends Object
{
    use Operations\Create;
    use Operations\All;
    use Operations\Update;
    use Operations\Delete;

    /*
     * Sends a PDF statement to the customer
     *
     * @param array $opts
     *
     * @return array(Invoiced\Email)
     */
    public function sendStatement(array $opts = [])
    {
        $response = $this->_client->request('post', $this->_endpoint.'/emails', $opts);

        # build email objects
        $email = new Email($this->_client);

        return Util::buildObjects($email, $response['body']);
    }

    /**
     * Gets the customer's current balance.
     *
     * @return stdClass balance
     */
    public function balance()
    {
        $response = $this->_client->request('get', $this->_endpoint.'/balance');

        return $response['body'];
    }

    /**
     * Fetches the customer's subscriptions.
     *
     * @param array $opts
     *
     * @return [array(Invoiced\Object), Invoiced\Collection]
     */
    public function subscriptions(array $opts = [])
    {
        $response = $this->_client->request('get', $this->_endpoint.'/subscriptions', $opts);

        # build objects
        $subscription = new Subscription($this->_client);
        $subscriptions = Util::buildObjects($subscription, $response['body']);

        # store the metadata from the list operation
        $metadata = new Collection($response['headers']['Link'], $response['headers']['X-Total-Count']);

        return [$subscriptions, $metadata];
    }
}
