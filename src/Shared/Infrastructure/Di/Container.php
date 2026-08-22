<?php

namespace App\Shared\Infrastructure\Di;
use ReflectionClass;
use ReflectionNamedType;

final class Container {
    private array $binds = [];
    private array $instances = [];
    private array $inProgress = [];


    public function __construct(){}

    public function get(string $requiredClass): object {
        if(!$requiredClass) throw new \InvalidArgumentException("La clase no puede estar vacia");

        $resolvedParams = [];
        $targetClass = $requiredClass;
        $implementation = null; 
        
        if(array_key_exists($requiredClass,$this->binds)){
            $implementation = $this->binds[$requiredClass];
        }

        if(!$implementation){
            $implementation = $targetClass;
        }

        if(array_key_exists($targetClass,$this->instances)){
            return $this->instances[$targetClass];
        }

        if(isset($this->inProgress[$targetClass]) && $this->inProgress[$targetClass]){
            throw new \InvalidArgumentException("La clase: ".$targetClass." ya se esta implementando");
        }
        $this->inProgress[$targetClass] = true;

        try{
            if(is_callable($implementation)){
                $this->instances[$targetClass] = $implementation($this);
            }else{
                if(!class_exists($implementation) && !interface_exists($implementation)) throw new \InvalidArgumentException("La clase no existe: ".$implementation);
                
                $reflection = new ReflectionClass($implementation);
                if(!$reflection->isInstantiable()){
                    throw new \InvalidArgumentException("Falta registrar un binding para: ".$implementation);
                }

                $constructor = $reflection->getConstructor();
                if($constructor){
                    $params = $constructor->getParameters();
                    if($params){
                        
                        foreach($params as $value){
                            if($value->hasType() && $value->getType() instanceof ReflectionNamedType && !$value->getType()->isBuiltin()){
                                $paramType = $value->getType();
                                $typeName = $paramType->getName();
                                $resolvedParams[] = $this->get($typeName);
                            }else if($value->isDefaultValueAvailable()){
                                $resolvedParams[] = $value->getDefaultValue();
                            }else if($value->allowsNull()){
                                $resolvedParams[] = null;
                            }else{
                                throw new \InvalidArgumentException("No se pudo resolver el parámetro: ".$value->getName()." de la clase: ".$implementation);
                            }
                        }
                        $this->instances[$targetClass] = $reflection->newInstanceArgs($resolvedParams);
                    } else {
                        $this->instances[$targetClass] = $reflection->newInstance();
                    }
                }else{
                    $this->instances[$targetClass] = $reflection->newInstanceWithoutConstructor();
                }
            }
            return $this->instances[$targetClass];
        }finally{
            unset($this->inProgress[$targetClass]);
        }

    }

    public function bind(string $interface, string | callable $implementation){
        $this->binds[$interface] = $implementation;
    }
}