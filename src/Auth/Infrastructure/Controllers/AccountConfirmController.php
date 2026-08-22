<?php

namespace App\Auth\Infrastructure\Controllers;
use App\Auth\Application\UseCase\AccountConfirm;
use App\Shared\Infrastructure\Http\Response;
use App\Shared\Infrastructure\Http\PayloadValidator;
use App\Shared\Infrastructure\Http\Request;


final class AccountConfirmController {
    public function __construct(
        private readonly AccountConfirm $accountConfirm,
        private readonly Request $req
    ) {}

    public function execute():void {
        PayloadValidator::validate($this->req->query(),['confirmation']);
        $token = $this->req->query()['confirmation'];
        $this->accountConfirm->confirmAccount($token);
        $response = new Response(
            "Cuenta confirmada, ya puede iniciar sesión"
        );
        $response->send();
    }
}