<?php

namespace Invoiced;

class SignUpPage extends BaseObject
{
    use Operations\Create;
    use Operations\All;
    use Operations\Update;
    use Operations\Delete;

    protected $_endpoint = '/sign_up_pages';
}
