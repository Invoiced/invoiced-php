<?php

namespace Invoiced;

class Item extends BaseObject
{
    use Operations\Create;
    use Operations\All;
    use Operations\Update;
    use Operations\Delete;
}
