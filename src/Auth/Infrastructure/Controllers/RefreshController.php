<?php

namespace App\Auth\Infrastructure\Controllers;
use App\Auth\Application\UseCase\Refresh;
use App\Shared\Infrastructure\Http\Response;

final class RefreshController {
    public function __construct(
        private readonly Refresh $refresh
    ) {}

    public function execute():void {
        $loginResponse = $this->refresh->refresh(
            $_SERVER['HTTP_USER_AGENT']
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