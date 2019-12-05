<?php

namespace Invoiced;

class PaymentSource extends BaseObject
{

    /**
     * Creates an object. This variant creates the appropriate subtype if applicable.
     *
     * @param array $params
     * @param array $opts
     *
     * @return object newly created object
     */
    public function create(array $params = [], array $opts = [])
    {
        $response = $this->_client->request('post', $this->getEndpoint(), $params, $opts);

        $obj = $response['body'];

        if ($obj['object'] == 'card')
        {
            $card = new Card($this->getClient());
            $card->setEndpointBase($this->getEndpointBase());
            return Util::convertToObject($card, $obj);
        }
        elseif ($obj['object'] == 'bank_account')
        {
            $acct = new BankAccount($this->getClient());
            $acct->setEndpointBase($this->getEndpointBase());
            return Util::convertToObject($acct, $obj);
        }
        else return Util::convertToObject($this, $obj);
    }

    /**
     * Fetches a collection of objects. This variant constructs them into Card or BankAccount objects as appropriate.
     *
     * @param array $opts
     *
     * @return [array(Invoiced\Object), Invoiced\Collection]
     */
    public function all(array $opts = [])
    {
        $response = $this->_client->request('get', $this->getEndpoint(), $opts);

        $output = [];

        // create card and bank account objects appropriately
        foreach ($response['body'] as $obj) {
            if ($obj['object'] == 'card') {
                $card = new Card($this->getClient());
                $card->setEndpointBase($this->getEndpointBase());
                $output[] = Util::convertToObject($card, $obj);
            } elseif ($obj['object'] == 'bank_account') {
                $acct = new BankAccount($this->getClient());
                $acct->setEndpointBase($this->getEndpointBase());
                $output[] = Util::convertToObject($acct, $obj);
            }
        }

        // store the metadata from the list operation
        $metadata = new Collection($response['headers']['Link'], $response['headers']['X-Total-Count']);

        return [$output, $metadata];
    }
}
