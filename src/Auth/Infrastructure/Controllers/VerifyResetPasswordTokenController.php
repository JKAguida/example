<?php

namespace App\Auth\Infrastructure\Controllers;
use App\Auth\Application\UseCase\VerifyResetPasswordToken;
use App\Shared\Infrastructure\Http\Response;
use App\Shared\Infrastructure\Http\PayloadValidator;
use App\Shared\Infrastructure\Http\Request;
use App\Shared\Infrastructure\Interfaces\HandlerInterface;


final class VerifyResetPasswordTokenController implements HandlerInterface{
    public function __construct(
        private readonly VerifyResetPasswordToken $tokenConfirm,
        private readonly Request $req
    ) {}

    public function execute():void {
        PayloadValidator::validate($this->req->query(),['confirmation']);
        $token = $this->req->query()['confirmation'];

        $this->tokenConfirm->verifyToken($token);
        $response = new Response(
            "Token válido, puede continuar con el cambio de contraseña."
        );
        $response->send();
    }
}