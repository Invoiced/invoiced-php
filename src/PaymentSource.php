<?php

namespace Invoiced;

class PaymentSource extends BaseObject
{
    use Operations\All;

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
