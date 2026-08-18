<?php

namespace App\Auth\Infrastructure\Controllers;
use App\Auth\Application\UseCase\AccountConfirm;
use App\Shared\Infrastructure\Http\Response;
use App\Shared\Infrastructure\Http\PayloadValidator;


final class AccountConfirmController {
    public function __construct(
        private readonly AccountConfirm $accountConfirm
    ) {}

    public function execute():void {
        PayloadValidator::validate($_GET,['confirmation']);
        $token = $_GET['confirmation'];
        $this->accountConfirm->confirmAccount($token);
        $response = new Response(
            "Cuenta confirmada, ya puede iniciar sesión"
        );
        $response->send();
    }
}