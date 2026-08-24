<?php

namespace App\Shared\Infrastructure\Http;
use App\Shared\Infrastructure\Exception\BadConfigurationException;

final class Request{
    private ?string $userId = null;
    
    /**
     * @param ?array<string,mixed> $body
     * @param ?array<string,string> $query
     */
    private function __construct(
        private readonly string $method,
        private readonly string $path,
        private readonly ?array $body,
        private readonly ?array $query,
        private readonly string $ip,
        private readonly ?string $authorization,
        private readonly string $userAgent,
    ){}

    public static function create():self {
        $request_method = $_SERVER['REQUEST_METHOD'];
        $path = $_SERVER['PATH_INFO'] ?? $_SERVER['REQUEST_URI'];
        $path_clean = explode('?',$path)[0];
        $bodyRecived = file_get_contents('php://input');
        if(!$bodyRecived){
            $bodyRecived = "";
        }
        $data = json_decode($bodyRecived,true);
        $headers = self::getAuthorizationHeader();
        return new self(
            method:$request_method,
            path:$path_clean,
            body:$data,
            query:$_GET,
            ip:$_SERVER['REMOTE_ADDR'],
            userAgent: isset($_SERVER['HTTP_USER_AGENT']) ? $_SERVER['HTTP_USER_AGENT'] : 'unknown',
            authorization:$headers
        );
    }

    /**
     * @param ?array<string,mixed> $body
     * @param ?array<string,string> $query
     */
    public static function reconstitute(
        string $method,
        string $path,
        ?array $body,
        ?array $query,
        string $ip,
        ?string $authorization,
        ?string $userAgent,
    ):self {
        return new self(
            method:$method,
            path:$path,
            body:$body,
            query:$query,
            ip:$ip,
            userAgent:$userAgent ? $userAgent : 'unknown',
            authorization:$authorization
        );
    }

    public function setUserId(string $id):void{
        if($this->userId) return;
        $this->userId = $id;
    }

    public function method():string {return $this->method; }
    public function path():string {return $this->path; }
    /**
     * @return ?array<string,mixed> 
     */
    public function body():?array {return $this->body; }
    /**
     * @return ?array<string,string> 
     */
    public function query():?array {return $this->query; }
    public function ip():string {return $this->ip; }
    public function userAgent():string {return $this->userAgent; }
    public function authorization():?string {return $this->authorization; }
    public function userId():?string {return $this->userId; }

    private static function getAuthorizationHeader():?string {
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

        return $headers;
    }

}
