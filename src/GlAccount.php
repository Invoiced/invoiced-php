<?php

namespace Invoiced;

class GlAccount extends BaseObject
{
    use Operations\Create;
    use Operations\All;
    use Operations\Update;
    use Operations\Delete;

    protected $_endpoint = '/gl_accounts';
}
