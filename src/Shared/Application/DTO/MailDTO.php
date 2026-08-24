<?php 

namespace App\Shared\Application\DTO;

final class MailDTO {
    public function __construct(
        /** @var list<string> */
        private readonly array $recipients,
        private readonly string $subject,
        private readonly string $messageBody,
        private readonly string $altBody
    ) {}

    /** @return list<string> */
    public function recipients() :array  { return $this->recipients; }
    public function subject() :string { return $this->subject; }
    public function messageBody() :string { return $this->messageBody; }
    public function altBody() :string { return $this->altBody; }
}