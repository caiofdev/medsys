<?php

namespace App\Domain\Exceptions;

use Exception;

class CannotDeleteLastMasterException extends Exception
{
    protected $message = 'Não é possível deletar o último administrador master do sistema.';
    protected $code = 422;
}

