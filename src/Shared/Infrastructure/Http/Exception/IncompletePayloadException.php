<?php

namespace App\Shared\Infrastructure\Http\Exception;
use App\Shared\Domain\Exception\ExceptionContextInterface;

/** @phpstan-import-type ArrayContextException from ExceptionContextInterface */
final class IncompletePayloadException extends \Exception implements ExceptionContextInterface {
    /** @var ArrayContextException */
    private array $contextData;
    /** @param ArrayContextException $context */
    public function __construct(string $msg, array $context){
        parent::__construct(message:$msg);
        $this->contextData = $context;
    }
    /** @return array<string,ArrayContextException> */
    public function context():array{
        return ["missingFields"=>$this->contextData];
    }

}