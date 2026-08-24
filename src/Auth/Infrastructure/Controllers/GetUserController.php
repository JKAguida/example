<?php

namespace App\Auth\Infrastructure\Controllers;
use App\Auth\Application\UseCase\GetUser;
use App\Shared\Infrastructure\Http\Response;
use App\Shared\Infrastructure\Http\Request;
use App\Shared\Infrastructure\Middleware\RequiresAuthenticationInterface;
use App\Shared\Infrastructure\Interfaces\HandlerInterface;



final class GetUserController implements RequiresAuthenticationInterface,HandlerInterface {
    public function __construct(
        private readonly GetUser $getUser,
        private readonly Request $req
    ){}

    public function execute(): void {

        $getUserResponseDTO = $this->getUser->execute($this->req->userId());
        $response = new Response(
            msg: "Se ha recuperado el usuario correctamente.",
            data: [
                "userName"=>$getUserResponseDTO->userName(),
                "lastName"=>$getUserResponseDTO->lastName(),
                "email"=>$getUserResponseDTO->email(),
                "roles"=>$getUserResponseDTO->roles(),
            ]
        );
        $response->send();
    }
}