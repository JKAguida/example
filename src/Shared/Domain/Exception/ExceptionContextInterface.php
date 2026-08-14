<?php

namespace App\Shared\Domain\Exception;


interface ExceptionContextInterface {
    public function context():array;
}