<?php

namespace App\Auth\Infrastructure\Controllers;
use App\Auth\Application\UseCase\Login;
use App\Auth\Application\DTO\LoginRequestDTO;
use App\Shared\Infrastructure\Http\Response;
use App\Shared\Infrastructure\Http\PayloadValidator;


final class LoginController {
    public function __construct(
        private readonly Login $login
    ){}

    public function execute(): void {
        $data = json_decode(file_get_contents("php://input"),true);
        PayloadValidator::validate($data,['email','rawPassword']);
        $loginDTO = new LoginRequestDTO(
            $data['email'],
            $data['rawPassword'],
            $_SERVER['HTTP_USER_AGENT']
        );

        $loginResponseDTO = $this->login->login($loginDTO);
        $response = new Response(
            msg: "Inicio de sessión exitoso",
            data: [
                "accessToken" => $loginResponseDTO->accessToken()
            ]
        );
        $response->send();
    }
}