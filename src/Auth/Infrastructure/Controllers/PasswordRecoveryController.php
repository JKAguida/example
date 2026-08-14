<?php

namespace App\Auth\Infrastructure\Controllers;
use App\Auth\Application\UseCase\PasswordRecovery;
use App\Shared\Infrastructure\Http\Response;

final class PasswordRecoveryController {
    public function __construct(
        private readonly PasswordRecovery $passwordRecovery
    ){}

    public function execute(): void {
        $data = json_decode(file_get_contents('php://input'),true);
        $this->passwordRecovery->passwordRecoveryRequest($data['email']);
        $response = new Response(
            msg: "Se ha enviado un email para continuar el proceso"
        );
        $response->send();
    }
}