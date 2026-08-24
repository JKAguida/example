<?php

namespace App\Auth\Infrastructure\Controllers;
use App\Auth\Application\UseCase\Refresh;
use App\Shared\Infrastructure\Http\Response;
use App\Shared\Infrastructure\Http\Request;
use App\Shared\Infrastructure\Interfaces\HandlerInterface;

final class RefreshController implements HandlerInterface{
    public function __construct(
        private readonly Refresh $refresh,
        private readonly Request $req
    ) {}

    public function execute():void {
        $loginResponse = $this->refresh->refresh(
            $this->req->userAgent(),
            $this->req->ip()
        );
        $response = new Response(
            msg: "Sesión actualizada",
            data: [
                "accessToken" => $loginResponse->accessToken()
            ]
        );
        $response->send();
    }
}