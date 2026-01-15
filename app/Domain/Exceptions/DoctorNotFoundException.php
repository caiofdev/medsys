<?php

namespace App\Domain\Exceptions;

use Exception;

class DoctorNotFoundException extends Exception
{
    protected $message = 'Médico não encontrado.';
    protected $code = 404;
}