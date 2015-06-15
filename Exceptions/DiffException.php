<?php

namespace JasonBradley\SpeedyDiff\Exceptions;

use Exception;

class DiffException extends Exception
{
    /**
     * @param string    $message
     * @param int       $code
     * @param Exception $previous
     */
    public function __construct($message = '', $code = null, Exception $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }
}
