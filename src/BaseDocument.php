<?php

namespace Invoiced;

/**
 * @property string $number
 * @property bool   $draft
 * @property bool   $closed
 * @property bool   $paid
 * @property bool   $voided
 * @property string $status
 * @property float  $subtotal
 * @property float  $total
 */
abstract class BaseDocument extends BaseObject
{
    use Operations\Create;
    use Operations\All;
    use Operations\Update;
    use Operations\Delete;

    /**
     * Fetches the document's file attachments.
     *
     * @param array<mixed> $opts
     *
     * @return array<mixed> [array(Invoiced\Object), Invoiced\Collection]
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
     * Voids the document.
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
