<?php

namespace App\Exceptions;

use Exception;

class InsufficientBalance extends Exception
{
    public function __construct($message = 'Saldo insuficiente.', $code = 400, Exception $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }
}
