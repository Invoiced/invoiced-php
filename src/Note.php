<?php

namespace Invoiced;

class Note extends BaseObject
{
    use Operations\Create;
    use Operations\All;
    use Operations\Update;
    use Operations\Delete;
}
