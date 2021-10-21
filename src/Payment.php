<?php

namespace Invoiced;

/**
 * @property string|null $ach_sender_id
 * @property float       $amount
 * @property float       $balance
 * @property int|null    $charge
 * @property int         $created_at
 * @property string      $currency
 * @property int|null    $customer
 * @property int         $date
 * @property bool        $matched
 * @property string      $method
 * @property string|null $notes
 * @property string      $pdf_url
 * @property string|null $reference
 * @property string      $source
 * @property string      $status
 * @property bool        $voided
 * @property int         $updated_at
 */
class Payment extends BaseObject
{
    use Operations\Create;
    use Operations\All;
    use Operations\Update;
    use Operations\Delete;

    protected $_endpoint = '/payments';

    /**
     * Sends a payment receipt.
     *
     * @param array<mixed> $params
     * @param array<mixed> $opts
     *
     * @return Email[]
     */
    public function send(array $params = [], array $opts = [])
    {
        $response = $this->_client->request('post', $this->getEndpoint().'/emails', $params, $opts);

        // build email objects
        $email = new Email($this->_client);

        return Util::buildObjects($email, $response['body']); /* @phpstan-ignore-line */
    }
}
