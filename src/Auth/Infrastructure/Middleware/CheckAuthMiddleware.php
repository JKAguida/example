<?php
namespace App\Auth\Infrastructure\Middleware;
use App\Auth\Domain\Exception\InvalidTokenException;
use App\Auth\Application\Security\TokenValidatorInterface;
use App\Auth\Domain\ValueObject\UserId;
use App\Shared\Infrastructure\Http\Request;
use App\Shared\Infrastructure\Interfaces\HandlerInterface;



final class CheckAuthMiddleware implements HandlerInterface{
    public function __construct(
        private readonly TokenValidatorInterface $tokenValidator,
        private readonly Request $req
    ){}

    public function execute():void {
        $token = $this->getBearerToken($this->req->authorization());
        if(!$token) throw new InvalidTokenException("No se encontro el token");
        $decoded = $this->tokenValidator->verify($token);
        $this->req->setUserId($decoded['sub']);
    }

    private function getBearerToken(?string $headers):?string {
        // Extraer el token de la cabecera "Bearer <token>"
        if (!empty($headers)) {
            if (preg_match('/^Bearer\s(\S+)/i', $headers, $matches)) {
                return $matches[1];
            }
        }
        
        return null;
    }
}