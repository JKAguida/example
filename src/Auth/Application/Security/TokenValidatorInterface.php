<?php

namespace App\Auth\Application\Security;

interface TokenValidatorInterface {
    public function verify(string $jwt):array;
}