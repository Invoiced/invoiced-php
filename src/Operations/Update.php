<?php

namespace Invoiced\Operations;

use Invoiced\Error\ErrorBase;

trait Update
{
    /**
     * Saves the object.
     *
     * @param array<mixed> $params
     * @param array<mixed> $opts
     *
     * @throws ErrorBase
     *
     * @return bool
     */
    public function save(array $params = [], array $opts = [])
    {
        $update = [];

        foreach ($this->_unsaved as $k) {
            $update[$k] = $this->_values[$k];
        }

        $update = array_replace($update, $params);

        // perform the update if there are any changes
        if (count($update) > 0) {
            $response = $this->_client->request('patch', $this->getEndpoint(), $update, $opts);

            // update the local values with the response
            $this->_values = array_replace((array) $response['body'], ['id' => $this->id]);
            $this->_unsaved = [];

            return 200 == $response['code'];
        }

        return false;
    }
}
