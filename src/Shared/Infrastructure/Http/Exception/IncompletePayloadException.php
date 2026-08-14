<?php

namespace App\Shared\Infrastructure\Http\Exception;
use App\Shared\Domain\Exception\ExceptionContextInterface;

final class IncompletePayloadException extends \Exception implements ExceptionContextInterface {
    private array $contextData;
    public function __construct(string $msg, array $context){
        parent::__construct(message:$msg);
        $this->contextData = $context;
    }
    public function context():array{
        return ["missingFields"=>$this->contextData];
    }

}