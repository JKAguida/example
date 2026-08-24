<?php

namespace App\Shared\Infrastructure\Di;
use ReflectionClass;
use ReflectionNamedType;

final class Container {

    /** @var array<class-string,class-string|callable> */
    private array $binds = [];
    /** @var array<object> */
    private array $instances = [];
    /** @var array<string,bool> */
    private array $inProgress = [];


    public function __construct(){}
    /**
     * @template T of object
     * @param class-string<T> $requiredClass
     * @return T
     */
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
                                if(!class_exists($typeName) && !interface_exists($typeName)){
                                    throw new \InvalidArgumentException("No existe la clase del parámetro: ".$value->getName()." de la clase: ".$implementation);
                                }
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

    /** 
     * @param class-string $interface
     * @param class-string|callable $implementation
     */
    public function bind(string $interface, string | callable $implementation):void{
        if(!interface_exists($interface)){
            if(!class_exists($interface)){
                throw new \InvalidArgumentException("La key esperada no esta definida: ".$interface);
            }
        }

        if(!is_callable($implementation)){
            if(!class_exists($implementation)){
                throw new \InvalidArgumentException("La implementación no esta definida: ".$implementation);
            }
        }
        $this->binds[$interface] = $implementation;
    }
}