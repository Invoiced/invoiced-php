<?php

namespace Invoiced\Error;

use Exception;

class ErrorBase extends Exception
{
    private $statusCode;
    private $error;

    public function __construct($message, $statusCode = null, $error = null)
    {
        parent::__construct($message);

        $this->statusCode = $statusCode;
        $this->error = $error;
    }
}
