<?php

namespace App\Auth\Infrastructure\Controllers;

use App\Auth\Application\UseCase\ResendConfirmationAccountToken;
use App\Shared\Infrastructure\Http\Response;


final class ResendConfirmationAccountTokenController {
    public function __construct(
        private readonly ResendConfirmationAccountToken $useCase
    ){}    

    public function execute(){
        $data = json_decode(file_get_contents("php://input"),true);
        try {
            $this->useCase->execute($data['email']);
            $response = new Response(
                msg:"Si los datos son correctos, recibiras un email para que confirmes tu cuenta"
            );
            $response->send(200);
        } catch (\Throwable $th) {
            if($th instanceof \InvalidArgumentException){
                $response = new Response(
                    msg: $th->getMessage(),
                    status: 'error'
                );
                $response->send(400);
            }else{
                $response = new Response(
                    msg: $th->getMessage(),
                    status: 'error'
                );
                $response->send(500);
            }
        }
    }
}