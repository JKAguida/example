<?php

namespace App\Auth\Application\DTO;

final class GetUserResponseDTO {
    public function __construct(
        private readonly string $userName,
        private readonly string $lastName,
        private readonly string $email,
        private readonly array $roles,
    ){}

    public function userName():string { return $this->userName; }
    public function lastName():string { return $this->lastName; }
    public function email():string { return $this->email; }
    public function roles():array { return $this->roles; }
}