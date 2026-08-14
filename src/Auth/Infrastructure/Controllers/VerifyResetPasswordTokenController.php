<?php

namespace App\Auth\Infrastructure\Controllers;
use App\Auth\Application\UseCase\VerifyResetPasswordToken;
use App\Shared\Infrastructure\Http\Response;
use App\Auth\Domain\Exception\InvalidTokenException;

final class VerifyResetPasswordTokenController {
    public function __construct(
        private readonly VerifyResetPasswordToken $tokenConfirm
    ) {}

    public function execute():void {
        $token = $_GET['confirmation'];
        $this->tokenConfirm->verifyToken($token);
        $response = new Response(
            "Token válido, puede continuar con el cambio de contraseña."
        );
        $response->send();
    }
}