<?php

namespace App\Shared\Infrastructure\Http;

use App\Shared\Infrastructure\Http\Response;

use App\Shared\Domain\Exception\CorruptedPersistedDataException;
use App\Shared\Domain\Exception\InvalidInputException;

use App\Auth\Domain\Exception\AccountNotVerifiedException;
use App\Auth\Domain\Exception\EmailAlreadyExistsException;
use App\Auth\Domain\Exception\InvalidCredentialsException;
use App\Auth\Domain\Exception\InvalidTokenException;
use App\Auth\Domain\Exception\InvalidTokenTypeException;
use App\Auth\Domain\Exception\TokenExpiredException;

final class HandlerExceptions {
    private array $code_map = [
        CorruptedPersistedDataException::class => [
            'status_code' => 500,
            'code' => 'CORRUPTED_DATA',
            'msg' => 'Ha ocurrido un error interno, intente mas tarde.'
        ],
        InvalidInputException::class => [
            'status_code' => 400,
            'code' => 'INVALID_INPUT',
            'msg' => 'El valor recibido no es válido, ingrese un valor correcto.'
        ],
        AccountNotVerifiedException::class => [
            'status_code' => 401,
            'code' => 'NOT_VERIFIED',
            'msg' => 'La cuenta aún no ha sido verificada, revise su email para confirmar la cuenta.'
        ],
        EmailAlreadyExistsException::class => [
            'status_code'=> 409,
            'code' => 'UNIQUE_EXCEPTION',
            'msg' => 'El usuario ya esta registrado.'
        ],
        InvalidCredentialsException::class => [
            'status_code'=> 401,
            'code' => 'BAD_CREDENTIALS',
            'msg' => 'Las credenciales de autenticación son incorrectas.'
        ],
        InvalidTokenException::class => [
            'status_code'=> 401,
            'code' => 'TOKEN_INVALID',
            'msg' => 'El token no es válido.'
        ],
        InvalidTokenTypeException::class => [
            'status_code'=> 401,
            'code' => 'TOKEN_INVALID',
            'msg' => 'El token no es válido.'
        ],
        TokenExpiredException::class => [
            'status_code'=> 401,
            'code' => 'TOKEN_EXPIRED',
            'msg' => 'El token ha expirado, solicite uno nuevo.'
        ],
    ];

    public function handle(
        \Throwable $exception,
        string $method,
        string $path
    ):Response{
        $exceptionClass = $exception::class;
        if(!isset($this->code_map[$exceptionClass])){
            $msg = "Ha ocurrido un error en el servidor.";
            $status_code = 500;    
            $code = "SERVER_ERROR";    
            $status = 'error';
        }else{
            $msg = $this->code_map[$exceptionClass]['msg'];
            $status_code = $this->code_map[$exceptionClass]['status_code'];    
            $code = $this->code_map[$exceptionClass]['code'];    
            $status = 'error';
        }

        $prev = $exception->getPrevious();
        error_log("[".$status_code."][".$code."] [".$method ." - ". $path."]: ".$exception->getMessage().(isset($prev)?$prev:''));
        
        return new Response(
            msg:$msg,
            status_code:$status_code,
            code:$code,
            status:$status
        );
    }
}