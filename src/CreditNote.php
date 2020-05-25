<?php

namespace Invoiced;

class CreditNote extends BaseObject
{
    use Operations\Create;
    use Operations\All;
    use Operations\Update;
    use Operations\Delete;

    protected $_endpoint = '/credit_notes';

    /*
     * Sends the credit note to the customer,
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
     * Fetches the credit note's file attachments.
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
     * Voids the credit note.
     *
     * @return CreditNote
     */
    public function void()
    {
        $response = $this->_client->request('post', $this->getEndpoint().'/void', [], []);

        // update the local values with the response
        $this->_values = array_replace((array) $response['body'], ['id' => $this->id]);

        return 200 == $response['code'];
    }
}
