<?php

namespace App\Shared\Infrastructure\Http;
use App\Shared\Infrastructure\Http\Exception\IncompletePayloadException;

final class PayloadValidator {
    /** 
     * @param ?array<string,mixed> $payload
     * @param list<string> $expectedData
     * 
    */
    public static function validate(?array $payload,array $expectedData):void{
        $notFound = [];
        if(!$payload) throw new IncompletePayloadException("El payload viene vacío. Se esperaban: ".json_encode($expectedData),$expectedData);
        foreach($expectedData as $value){
            if(!isset($payload[$value])) $notFound[] = $value;
        }

        if(count($notFound)>0){
            throw new IncompletePayloadException("El payload no esta completo, faltan: ".json_encode($notFound),$notFound);
        }
    }
}