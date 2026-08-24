<?php

namespace App\Auth\Infrastructure\Controllers;
use App\Auth\Application\UseCase\PasswordRecovery;
use App\Shared\Infrastructure\Http\Response;
use App\Shared\Infrastructure\Http\PayloadValidator;
use App\Shared\Infrastructure\Http\Request;
use App\Shared\Infrastructure\Interfaces\HandlerInterface;


final class PasswordRecoveryController implements HandlerInterface{
    public function __construct(
        private readonly PasswordRecovery $passwordRecovery,
        private readonly Request $req
    ){}

    public function execute(): void {
        $data = $this->req->body();
        PayloadValidator::validate($data,['email']);
        
        $this->passwordRecovery->passwordRecoveryRequest($data['email']);
        $response = new Response(
            msg: "Se ha enviado un email para continuar el proceso"
        );
        $response->send();
    }
}