<?php

namespace Invoiced\Error;

use Exception;

class ErrorBase extends Exception
{
    /**
     * @var int|null
     */
    private $statusCode;

    /**
     * @var string|null
     */
    private $error;

    /**
     * @param string      $message
     * @param int|null    $statusCode
     * @param string|null $error
     */
    public function __construct($message, $statusCode = null, $error = null)
    {
        parent::__construct($message);

        $this->statusCode = $statusCode;
        $this->error = $error;
    }
}
