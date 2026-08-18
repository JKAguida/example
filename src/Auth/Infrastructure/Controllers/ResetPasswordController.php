<?php

namespace App\Auth\Infrastructure\Controllers;
use App\Auth\Application\UseCase\ResetPassword;
use App\Shared\Infrastructure\Http\Response;
use App\Shared\Infrastructure\Http\PayloadValidator;


final class ResetPasswordController {
    public function __construct(
        private readonly ResetPassword $resetPassword
    ){}

    public function execute(): void {
        PayloadValidator::validate($_GET,['confirmation']);
        $token = $_GET['confirmation'];

        $data = json_decode(file_get_contents('php://input'),true);
        PayloadValidator::validate($data,['rawPassword']);

        $this->resetPassword->reset(
            $token,
            $data['rawPassword']
        );
        $response = new Response(
            msg: "Contraseña actualizada",
        );
        $response->send();
    }
}