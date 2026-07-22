<?php

namespace App\Core;

use App\Core\Interfaces\ContainerInterface;
use App\Services\ServiceNotFoundException;
use ReflectionClass;
use ReflectionNamedType;

class Container implements ContainerInterface
{
    /**
     * @param array<string, \Closure(ContainerInterface): mixed> $factories
     * @param array<string, mixed> $instances
     */
    public function __construct(
        private array $factories = [],
        private array $instances = []
    ) {
    }

    /**
     * @param callable(ContainerInterface): mixed $factory
     */

    public function set(string $id, callable $factory): void
    {
        $this->factories[$id] = $factory;
    }

    public function has(string $id): bool
    {
        if (array_key_exists($id, $this->instances) or array_key_exists($id, $this->factories)) {
            return true;
        } return false;
    }

    public function get(string $id): mixed
    {
        if (array_key_exists($id, $this->instances)) {
            return $this->instances[$id];
        }
        if (array_key_exists($id, $this->factories)) {
            $factory = $this->factories[$id];
            $obj = $factory($this);
            $this->instances[$id] = $obj;
            return $obj;
        }
        /**
         * @param string $id 
         * @return mixed
         * @throws ServiceNotFoundException
         */
        if (!class_exists($id) && !interface_exists($id)) {
            throw new ServiceNotFoundException("Class or interface {$id} does not exist.");
        }

        if (!$reflector->isInstantiable()) {
            throw new ServiceNotFoundException("Class {$id} is not instantiable.");
        }
        $constructor = $reflector->getConstructor();
        if (!$constructor) {
            $obj = $reflector->newInstance();
            $this->instances[$id] = $obj;
            return $obj;
        }
        $parameters = $constructor->getParameters();
        $dependencies = [];
        foreach ($parameters as $param) {
            $type = $param->getType();
            if (!$type instanceof ReflectionNamedType) {
                if ($param->isDefaultValueAvailable()) {
                    $dependencies[] = $param->getDefaultValue();
                    continue;
                }
                throw new ServiceNotFoundException("Parameter {$param->getName()} has an untyped or complex dependency.");
            }

            if ($type->isBuiltin()) {
                throw new ServiceNotFoundException("Built-in parameter {$param->getName()} has no default value.");
            } else {
                $typeName = $type->getName();
                $dependencies[] = $this->get($typeName);
            }
        }
        $obj = $reflector->newInstanceArgs($dependencies);
        $this->instances[$id] = $obj;
        return $obj;
    }
}
