<?php

namespace Invoiced;

class LineItem extends Object
{
    use Operations\Create;
    use Operations\All;
    use Operations\Update;
    use Operations\Delete;

    /**
     * @var Customer
     */
    private $customer;

    /**
     * @param Invoiced\Client $client   API client instance
     * @param string          $id
     * @param array           $values
     * @param Customer        $customer
     */
    public function __construct(Client $client, $id = null, array $values = [], Customer $customer = null)
    {
        parent::__construct($client, $id, $values);

        $this->customer = $customer;
        if ($customer) {
            $this->_endpoint = '/customers/'.$customer->id.$this->_endpoint;
        }
    }
}
