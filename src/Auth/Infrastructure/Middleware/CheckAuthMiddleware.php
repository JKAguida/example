<?php
namespace App\Auth\Infrastructure\Middleware;
use App\Auth\Domain\Exception\InvalidTokenException;
use App\Auth\Application\Security\TokenValidatorInterface;
use App\Auth\Domain\ValueObject\UserId;



final class CheckAuthMiddleware {
    public function __construct(
        private readonly TokenValidatorInterface $tokenValidator
    ){}

    public function execute():UserId {
        $token = $this->getBearerToken();
        if(!$token) throw new InvalidTokenException("No se encontro el token");
        $decoded = $this->tokenValidator->verify($token);
        return UserId::fromString($decoded['sub']);
    }

    private function getBearerToken():?string {
        $headers = null;
        
        // 1. Obtener cabeceras estándar de Apache / Nginx / IIS
        if (function_exists('apache_request_headers')) {
            $requestHeaders = apache_request_headers();
            // Normalizar claves a minúsculas/mayúsculas según el entorno
            $requestHeaders = array_combine(
                array_map('ucwords', array_keys($requestHeaders)), 
                array_values($requestHeaders)
            );
            if (isset($requestHeaders['Authorization'])) {
                $headers = trim($requestHeaders['Authorization']);
            }
        } 
        
        // 2. Buscar en $_SERVER si no se encontró en las cabeceras
        if (!$headers && isset($_SERVER['HTTP_AUTHORIZATION'])) {
            $headers = trim($_SERVER['HTTP_AUTHORIZATION']);
        } elseif (!$headers && isset($_SERVER['REDIRECT_HTTP_AUTHORIZATION'])) { // Apache con mod_rewrite
            $headers = trim($_SERVER['REDIRECT_HTTP_AUTHORIZATION']);
        }

        // 3. Extraer el token de la cabecera "Bearer <token>"
        if (!empty($headers)) {
            if (preg_match('/^Bearer\s(\S+)/i', $headers, $matches)) {
                return $matches[1];
            }
        }
        
        return null;
    }
}