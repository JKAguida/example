<?php

namespace App\Auth\Application\DTO;

final class LoginRequestDTO {
    public function __construct(
        private readonly string $email,
        private readonly string $rawPassword,
        private readonly string $userAgent,
        private readonly string $ip,
    ){}

    public function email() :string { return $this->email; } 
    public function rawPassword() :string {return $this->rawPassword; }
    public function userAgent() :string { return $this->userAgent; }
    public function ip() :string { return $this->ip; }
}