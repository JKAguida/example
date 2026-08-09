<?php

namespace App\Auth\Domain\Exception;
use Exception;

final class InvalidTokenTypeException extends Exception {
    public function __construct(string $msg="El tipo de token no es el esperado."){
        parent::__construct($msg);
    }
}