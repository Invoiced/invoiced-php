<?php

namespace Invoiced;

class SignUpPageAddon extends BaseObject
{
    use Operations\Create;
    use Operations\All;
    use Operations\Update;
    use Operations\Delete;

    protected $_endpoint = '/sign_up_page_addons';
}
