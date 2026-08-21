<?php

namespace App\Auth\Infrastructure\Middleware;
use App\Auth\Domain\ValueObject\UserId;

interface AuthControllerContextInterface {
    public function setUserId(UserId $userId):void;
}