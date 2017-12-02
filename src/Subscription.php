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
}
