<?php

namespace Invoiced;

/**
 * @property bool        $closed
 * @property int         $created_at
 * @property int         $updated_at
 * @property string      $currency
 * @property int         $customer
 * @property int         $date
 * @property array       $discounts
 * @property bool        $draft
 * @property array       $items
 * @property object      $metadata
 * @property null|string $name
 * @property null|string $notes
 * @property int         $number
 * @property string      $pdf_url
 * @property null|string $purchase_order
 * @property string      $status
 * @property float       $subtotal
 * @property array       $taxes
 * @property float       $total
 * @property string      $url
 */
abstract class BaseDocument extends BaseObject
{
    use Operations\Create;
    use Operations\All;
    use Operations\Update;
    use Operations\Delete;
    use Operations\VoidDocument;

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
        $attachments = Util::buildObjects($attachment, $body); /* @phpstan-ignore-line */

        // store the metadata from the list operation
        $metadata = new Collection($response['headers']['Link'], $response['headers']['X-Total-Count']); /* @phpstan-ignore-line */

        return [$attachments, $metadata];
    }
}
