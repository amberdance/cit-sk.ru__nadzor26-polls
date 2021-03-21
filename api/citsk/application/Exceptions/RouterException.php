<?php

namespace Citsk\Exceptions;

use Exception;

class RouterException extends Exception
{
    public function __construct($message, $code = 200, Exception $previous = null)
    {

        parent::__construct($message, $code, $previous);
    }

}
