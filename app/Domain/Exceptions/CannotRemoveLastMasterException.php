<?php

namespace App\Domain\Exceptions;

use Exception;

class CannotRemoveLastMasterException extends Exception
{
    protected $message = 'Não é possível remover o status de master do último administrador master do sistema.';
    protected $code = 422;
}