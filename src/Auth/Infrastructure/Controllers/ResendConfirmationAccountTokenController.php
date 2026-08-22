<?php

namespace App\Auth\Infrastructure\Controllers;

use App\Auth\Application\UseCase\ResendConfirmationAccountToken;
use App\Shared\Infrastructure\Http\Response;
use App\Shared\Infrastructure\Http\PayloadValidator;
use App\Shared\Infrastructure\Http\Request;


final class ResendConfirmationAccountTokenController {
    public function __construct(
        private readonly ResendConfirmationAccountToken $useCase,
        private readonly Request $req
    ){}    

    public function execute():void{
        $data = $this->req->body();
        PayloadValidator::validate($data,['email']);

        $this->useCase->execute($data['email']);
        $response = new Response(
            msg:"Si los datos son correctos, recibiras un email para que confirmes tu cuenta"
        );
        $response->send();
    }
}