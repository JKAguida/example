<?php

namespace App\Auth\Application\Security;

interface TokenValidatorInterface {
    /** @return array{sub:string,iat:int} */
    public function verify(string $jwt):array;
}