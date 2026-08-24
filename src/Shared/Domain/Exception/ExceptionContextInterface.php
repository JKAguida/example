<?php

namespace App\Shared\Domain\Exception;

/** @phpstan-type ArrayContextException list<string> */
interface ExceptionContextInterface {
    /** @return array<string,ArrayContextException> */
    public function context():array;
}