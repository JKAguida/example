<?php

namespace App\Auth\Domain\ValueObject;
use App\Shared\Domain\Exception\InvalidInputException;

final class Email {
    private readonly string $value;

    private function __construct(string $value) {
        $this->value = $value;
    }

    public static function create(string $value) : self {
        $valueLowercase = mb_strtolower($value,'UTF-8');
        return filter_var($valueLowercase,FILTER_VALIDATE_EMAIL) === false ? throw new InvalidInputException("El formato de email no es válido") : new self($valueLowercase);   
    }

    public function value() : string {
        return $this->value;
    }

    public function equals(Email $other): bool {
        return $this->value() === $other->value();
    }
}