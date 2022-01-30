<?php

namespace Invoiced;

class LateFeeSchedule extends BaseObject
{
    use Operations\Create;
    use Operations\All;
    use Operations\Update;
    use Operations\Delete;

    protected $_endpoint = '/late_fee_schedules';
}
