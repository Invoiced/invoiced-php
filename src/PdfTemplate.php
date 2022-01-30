<?php

namespace Invoiced;

class PdfTemplate extends BaseObject
{
    use Operations\Create;
    use Operations\All;
    use Operations\Update;
    use Operations\Delete;

    protected $_endpoint = '/pdf_templates';
}
