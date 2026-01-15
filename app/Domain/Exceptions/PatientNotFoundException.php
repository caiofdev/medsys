<?php

namespace App\Domain\Exceptions;

use Exception;

class PatientNotFoundException extends Exception
{
    protected $message = 'Paciente não encontrado.';
    protected $code = 404;
}