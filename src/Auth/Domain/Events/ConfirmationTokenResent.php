<?php

namespace App\Auth\Domain\Events;
use App\Shared\Domain\Event\DomainEventInterface;
use App\Auth\Domain\ValueObject\TokenValue;
use App\Auth\Domain\ValueObject\Email;
use App\Auth\Domain\ValueObject\UserName;
use App\Shared\Domain\ValueObject\UUIDv7;
use DateTimeImmutable;

final class ConfirmationTokenResent extends DomainEventInterface {
    private function __construct(
        private readonly string $eventId,
        private readonly DateTimeImmutable $occurredAt,
        private readonly TokenValue $tokenValue,
        private readonly Email $email,
        private readonly UserName $userName,
    ) {}

    public static function create(UserId $userId,Email $email,UserName $userName) : self {
        $eventId = UUIDv7::generate();
        $now = new DateTimeImmutable();
        return new self($eventId->value(),$now,$userId,$email,$userName);
    }

    public function eventId() : string {
        return $this->eventId;
    }

    public function occurredAt() : DateTimeImmutable {
        return $this->occurredAt;
    }

    public function tokenValue() : TokenValue {
        return $this->userRegisteredId;
    }

    public function email() : Email {
        return $this->email;
    }

    public function userName() : UserName {
        return $this->userName;
    }
}