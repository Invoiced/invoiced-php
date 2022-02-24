<?php

namespace Invoiced\Operations;

use Invoiced\Error\ErrorBase;

trait VoidDocument
{
    /**
     * Voids the document.
     *
     * @throws ErrorBase
     *
     * @return bool
     */
    public function void()
    {
        $response = $this->_client->request('post', $this->getEndpoint().'/void', [], []);

        // update the local values with the response
        $this->_values = array_replace((array) $response['body'], ['id' => $this->id]);

        return 200 == $response['code'];
    }
}
