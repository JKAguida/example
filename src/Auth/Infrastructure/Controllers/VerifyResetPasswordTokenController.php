<?php

namespace App\Auth\Infrastructure\Controllers;
use App\Auth\Application\UseCase\VerifyResetPasswordToken;
use App\Shared\Infrastructure\Http\Response;
use App\Shared\Infrastructure\Http\PayloadValidator;


final class VerifyResetPasswordTokenController {
    public function __construct(
        private readonly VerifyResetPasswordToken $tokenConfirm
    ) {}

    public function execute():void {
        PayloadValidator::validate($_GET,['confirmation']);
        $token = $_GET['confirmation'];
        $this->tokenConfirm->verifyToken($token);
        $response = new Response(
            "Token válido, puede continuar con el cambio de contraseña."
        );
        $response->send();
    }
}