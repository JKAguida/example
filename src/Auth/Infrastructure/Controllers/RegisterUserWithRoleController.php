<?php

namespace App\Auth\Infrastructure\Controllers;
use App\Auth\Application\DTO\RegisterUserRequestDTO;
use App\Auth\Application\UseCase\RegisterUserWithRole;
use App\Shared\Infrastructure\Http\Response;
use App\Shared\Infrastructure\Http\PayloadValidator;
use App\Auth\Domain\ValueObject\UserId;
use App\Auth\Domain\ValueObject\RoleType;
use App\Auth\Infrastructure\Middleware\AuthControllerContextInterface;
use  App\Shared\Domain\Exception\InvalidInputException;




final class RegisterUserWithRoleController implements AuthControllerContextInterface{
    private readonly UserId $userId;

    public function __construct(
        private readonly RegisterUserWithRole $registerUser,
    ){}    

    public function execute():void{
        $data = json_decode(file_get_contents('php://input'),true);
        PayloadValidator::validate($data,['userName','lastName','email','rawPassword','roleType']);
        $registerUserRequestDTO = new RegisterUserRequestDTO(
            $data['userName'],
            $data['lastName'],
            $data['email'],
            $data['rawPassword']
        );

        $roleType = RoleType::tryFrom($data['roleType']);
        if(!$roleType) throw new InvalidInputException("El rol solicitado no es válido");
        
        $this->registerUser->execute($registerUserRequestDTO,$roleType,$this->userId);
        $response = new Response(
            msg: 'Usuario registrado, confirme su cuenta vía email.',
            status_code:201
        );
        $response->send();
    }

    public function setUserId(UserId $userId):void{
        $this->userId = $userId;
    }
}