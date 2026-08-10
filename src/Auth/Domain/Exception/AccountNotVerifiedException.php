<?php
namespace App\Auth\Domain\Exception;


final class AccountNotVerifiedException extends \Exception{
    public function __construct(string $msg = "La cuenta no ha sido confirmada."){
        parent::__construct($msg);
    }
}