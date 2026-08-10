<?php

namespace App\Auth\Domain\Exception;
use Exception;

final class TokenExpiredException extends Exception {
    public function __construct(string $msg="El token ha expirado."){
        parent::__construct($msg);
    }
}