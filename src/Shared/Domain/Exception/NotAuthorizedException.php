<?php

namespace App\Shared\Domain\Exception;

use Exception;

final class NotAuthorizedException extends Exception {
    public function __construct(string $msg="No estas autorizado para leer este recurso"){
        parent::__construct($msg);
    }
}