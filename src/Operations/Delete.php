<?php

namespace Invoiced\Operations;

use Invoiced\Error\ErrorBase;

trait Delete
{
    /**
     * Deletes the object.
     *
     * @throws ErrorBase
     *
     * @return bool
     */
    public function delete()
    {
        $response = $this->_client->request('delete', $this->getEndpoint());

        if ($response['code'] == 204) {
            $this->_values = ['id' => $this->id];
        } elseif ($response['code'] == 200) {
            // update the local values with the response
            $this->_values = array_replace((array) $response['body'], ['id' => $this->id]);
            $this->_unsaved = [];
        }

        return in_array($response['code'], [200, 204]);
    }
}
