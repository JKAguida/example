<?php

namespace App\Auth\Infrastructure\Controllers;
use App\Auth\Application\UseCase\ResetPassword;
use App\Shared\Infrastructure\Http\Response;
use App\Shared\Infrastructure\Http\PayloadValidator;
use App\Shared\Infrastructure\Http\Request;


final class ResetPasswordController {
    public function __construct(
        private readonly ResetPassword $resetPassword,
        private readonly Request $req
    ){}

    public function execute(): void {
        PayloadValidator::validate($this->req->query(),['confirmation']);
        $token = $this->req->query()['confirmation'];

        $data = $this->req->body();
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