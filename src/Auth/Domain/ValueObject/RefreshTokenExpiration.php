<?php


namespace App\Auth\Domain\ValueObject;
use DateTimeImmutable;
use App\Shared\Domain\Exception\CorruptedPersistedDataException;


final class RefreshTokenExpiration {
    private readonly DateTimeImmutable $expiration;

    private function __construct(DateTimeImmutable $value){
        $this->expiration = $value;
    }

    public static function generate() : self {
        $now = new DateTimeImmutable();
        $expiration = $now->modify('+7 days');
        return new self($expiration);
    }

    public static function fromString(string $strDateTime) : self {
        try {
            return new self(new DateTimeImmutable($strDateTime));
        } catch (\Exception $e) {
            throw new CorruptedPersistedDataException(
                message:"La fecha de la base de datos es inválida: " . $strDateTime,
                previous:$e
            );
        }
    }

    public function isExpired() : bool {
        $now = new DateTimeImmutable();
        return $now>$this->value();
    }

    public function value() : DateTimeImmutable {
        return $this->expiration;
    }
} 