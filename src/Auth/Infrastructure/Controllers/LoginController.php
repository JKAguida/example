<?php

namespace App\Auth\Infrastructure\Controllers;
use App\Auth\Application\UseCase\Login;
use App\Auth\Application\DTO\LoginRequestDTO;
use App\Shared\Infrastructure\Http\Response;
use App\Shared\Infrastructure\Http\PayloadValidator;
use App\Shared\Infrastructure\Http\Request;
use App\Shared\Infrastructure\Interfaces\HandlerInterface;


final class LoginController implements HandlerInterface{
    public function __construct(
        private readonly Login $login,
        private readonly Request $req
    ){}

    public function execute(): void {
        $data = $this->req->body();
        PayloadValidator::validate($data,['email','rawPassword']);
        $loginDTO = new LoginRequestDTO(
            $data['email'],
            $data['rawPassword'],
            $this->req->userAgent(),
            $this->req->ip()
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