<?php

namespace Invoiced;

class Subscription extends BaseObject
{
    use Operations\Create;
    use Operations\All;
    use Operations\Update;
    use Operations\Delete;

    protected $_endpoint = '/subscriptions';

    /**
     * Cancels the subscription.
     *
     * @return bool
     */
    public function cancel()
    {
        return $this->delete();
    }

    /**
     * Previews the subscription.
     *
     * @return Subscription
     */
    public function preview(array $params = [], array $opts = [])
    {
        $response = $this->_client->request('post', '/subscriptions/preview', $params, $opts);

        // return generic JSON (not actual subscription object)
        return json_decode(json_encode($response['body']), false);
    }
}
