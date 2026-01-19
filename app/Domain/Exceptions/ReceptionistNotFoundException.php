<?php

namespace App\Domain\Exceptions;

use Exception;

class ReceptionistNotFoundException extends Exception
{
    protected $message = 'Recepcionista não encontrado.';
    protected $code = 404;
}