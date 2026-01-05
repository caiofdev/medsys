<?php

namespace App\Domain\Exceptions;

use Exception;

class AdminNotFoundException extends Exception
{
    protected $message = 'Administrador não encontrado.';
    protected $code = 404;
}