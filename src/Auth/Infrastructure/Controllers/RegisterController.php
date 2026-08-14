<?php

namespace App\Auth\Infrastructure\Controllers;
use App\Auth\Application\DTO\RegisterUserRequestDTO;
use App\Auth\Application\UseCase\RegisterUser;
use App\Shared\Infrastructure\Http\Response;
use App\Auth\Domain\Exception\EmailAlreadyExistsException;

final class RegisterController{
    public function __construct(
        private readonly RegisterUser $registerUser,
    ){}    

    public function execute():void{
        $data = json_decode(file_get_contents('php://input'),true);
        $registerUserRequestDTO = new RegisterUserRequestDTO(
            $data['userName'],
            $data['lastName'],
            $data['email'],
            $data['rawPassword']
        );

        $response = null;
        
        $this->registerUser->register($registerUserRequestDTO);
        $response = new Response(
            msg: 'Usuario registrado, confirme su cuenta vía email.',
            status_code:201
        );
        $response->send();
    }
}