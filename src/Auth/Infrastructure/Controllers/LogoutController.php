<?php

namespace App\Auth\Infrastructure\Controllers;
use App\Auth\Application\UseCase\Logout;
use App\Shared\Infrastructure\Http\Response;


final class LogoutController {
    public function __construct(
        private readonly Logout $logout
    ){}

    public function execute(): void {
        $this->logout->logout();
        $response = new Response(
            msg: "Sesión cerrada."
        );
        $response->send();
    }
}