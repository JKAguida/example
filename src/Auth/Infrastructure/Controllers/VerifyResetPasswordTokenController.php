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
        try {
            $this->tokenConfirm->verifyToken($token);
            $response = new Response(
                "Token válido, puede continuar con el cambio de contraseña."
            );
            $response->send(200);
        } catch (\Throwable $th) {
            if($th instanceof InvalidTokenException ){
                $response = new Response(
                    msg:$th->getMessage(),
                    status:'error'
                );
                $response->send(404);
            }else{
                $response = new Response(
                    msg:$th->getMessage(),
                    status:'error'
                );
                $response->send(500);
            }
        }
    }
}