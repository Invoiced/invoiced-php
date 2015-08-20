<?php

namespace Invoiced\Operations;

trait Delete
{
    /**
     * Deletes the object.
     *
     * @return boolean
     */
    public function delete()
    {
        $response = $this->_client->request('delete', $this->_endpoint);

        if ($response['code'] == 204) {
            $this->_values = ['id' => $this->id];
        }

        return $response['code'] == 204;
    }
}
