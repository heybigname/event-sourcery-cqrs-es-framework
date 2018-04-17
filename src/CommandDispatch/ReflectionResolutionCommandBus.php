<?php namespace EventSourcery\CommandDispatch;

use Psr\Container\ContainerInterface as Container;
use ReflectionClass;

class ReflectionResolutionCommandBus implements CommandBus {

    /** @var Container */
    private $container;

    public function __construct(Container $container) {
        $this->container = $container;
    }

    public function execute(Command $c) {
        $c->execute(...$this->instantiateParameters($this->getExecutionParameters($c)));
    }

    private function getExecutionParameters(Command $c) {
        $reflect = new ReflectionClass(get_class($c));
        $method  = $reflect->getMethod('execute');
        return $method->getParameters();
    }

    private function instantiateParameters($params) {
        $classes = array_map(function(\ReflectionParameter $param) {
            return $param->getType()->getName();
        }, $params);

        return array_map(function($class) {
            return $this->container->get($class);
        }, $classes);
    }
}