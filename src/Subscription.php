<?php

namespace Invoiced;

class Subscription extends BaseObject
{
    use Operations\Create;
    use Operations\All;
    use Operations\Update;
    use Operations\Delete;

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

        // build subscription object
        $subscription = new Subscription($this->_client);

        return Util::convertPreviewToObject($subscription, $response['body']);
    }
}
