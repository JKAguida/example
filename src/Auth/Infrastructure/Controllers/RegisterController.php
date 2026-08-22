<?php

namespace App\Auth\Infrastructure\Controllers;
use App\Auth\Application\DTO\RegisterUserRequestDTO;
use App\Auth\Application\UseCase\RegisterUser;
use App\Shared\Infrastructure\Http\Response;
use App\Shared\Infrastructure\Http\PayloadValidator;
use App\Shared\Infrastructure\Http\Request;


final class RegisterController{
    public function __construct(
        private readonly RegisterUser $registerUser,
        private readonly Request $req
    ){}    

    public function execute():void{
        $data = $this->req->body();
        PayloadValidator::validate($data,['userName','lastName','email','rawPassword']);
        $registerUserRequestDTO = new RegisterUserRequestDTO(
            $data['userName'],
            $data['lastName'],
            $data['email'],
            $data['rawPassword']
        );
        
        $this->registerUser->execute($registerUserRequestDTO);
        $response = new Response(
            msg: 'Usuario registrado, confirme su cuenta vía email.',
            status_code:201
        );
        $response->send();
    }
}