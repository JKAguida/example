<?php

namespace App\Shared\Infrastructure\Di;
use ReflectionClass;
use ReflectionNamedType;

final class Container {
    private array $binds = [];
    private array $instances = [];

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
                        
                        if($value->hasType()){
                            $paramType = $value->getType();
                            if($paramType instanceof ReflectionNamedType){
                                $typeName = $paramType->getName();
                                $isBuiltin = $paramType->isBuiltin();

                                if (!$isBuiltin) {
                                    $resolvedParams[] = $this->get($typeName);
                                }
                            }
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
    }

    public function bind(string $interface, string | callable $implementation){
        $this->binds[$interface] = $implementation;
    }
}