<?php

namespace App\Auth\Infrastructure\Controllers;
use App\Auth\Application\UseCase\LogoutAll;
use App\Shared\Infrastructure\Http\Response;

final class LogoutAllController {
    public function __construct(
        private readonly LogoutAll $logoutAll
    ) {}

    public function execute():void {
        $this->logoutAll->logoutAll();
        $response = new Response(
            msg: "Todas las sesiones han sido cerradas"
        );
        $response->send();
    }
}