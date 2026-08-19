<?php

namespace App\Auth\Infrastructure\Controllers;

use App\Auth\Application\UseCase\ResendConfirmationAccountToken;
use App\Shared\Infrastructure\Http\Response;
use App\Shared\Infrastructure\Http\PayloadValidator;


final class ResendConfirmationAccountTokenController {
    public function __construct(
        private readonly ResendConfirmationAccountToken $useCase
    ){}    

    public function execute():void{
        $data = json_decode(file_get_contents("php://input"),true);
        PayloadValidator::validate($data,['email']);

        $this->useCase->execute($data['email']);
        $response = new Response(
            msg:"Si los datos son correctos, recibiras un email para que confirmes tu cuenta"
        );
        $response->send();
    }
}