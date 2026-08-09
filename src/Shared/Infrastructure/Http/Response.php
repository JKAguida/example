<?php

namespace App\Shared\Infrastructure\Http;

final class Response {
    public function __construct(
        private readonly string $msg,
        private readonly int $status_code=200,
        private readonly ?string $code = null,
        private readonly ?array $data = null,
        private readonly ?array $metadata = null,
        private readonly string $status = 'success'
    ) { }

    public function send() {
        http_response_code($this->status_code);
        header("Content-Type: application/json");
        $data = [
            "status"=>$this->status,
            "code"=>$this->code,
            "msg" => $this->msg,
            "data" => $this->data,
            "metadata" => $this->metadata
        ];
        echo(json_encode($data));
    }
}