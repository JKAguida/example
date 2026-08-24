<?php

namespace App\Shared\Infrastructure\Bootstrap;
use App\Shared\Infrastructure\Exception\BadConfigurationException;
final class EnvironmentLoader {
    private function __construct(){}
    public static function load():void{
        $path = __DIR__."/../../../../.env";
        $content = file_get_contents($path);
        if(!$content){
            throw new BadConfigurationException("No se encontraron las variables de entorno");
        }
        $arrayContent = explode("\n",$content);
        foreach($arrayContent as $value){
            //echo $value;
            if( preg_match('/^#/',$value) || $value==="" ) continue;
            putenv($value);
        }
    }
    public static function envOrFail(string $varName):string {
        $value = (string)getenv($varName);
        if($value===""){
            throw new BadConfigurationException("La variable ".$varName." no esta definida en las variables de entorno");
        }
        return $value;
    }
}