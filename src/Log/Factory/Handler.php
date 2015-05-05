<?php

namespace Dafiti\Silex\Log\Factory;

use Dafiti\Silex\Log\Logger;

class Handler
{
    private $args = [];

    public function __invoke(array $args)
    {
        $this->args = $args;

        if (!isset($this->args['class']) || empty($this->args['class'])) {
            throw new \OutOfBoundsException('You should define key class to create a log handler');
        }

        if (isset($this->args['params']['level'])) {
            $this->args['params']['level'] = Logger::translateLevel($this->args['params']['level']);
        }

        $class = new \ReflectionClass($args['class']);
        $params = $this->getParams($class->getConstructor());

        return $class->newInstanceArgs($params);
    }

    private function getParams(\ReflectionMethod $method)
    {
        $params = [];

        foreach ($method->getParameters() as $param) {
            $name = $param->getName();
            $params[$name] = $this->getParam($param);
        }

        return $params;
    }

    private function getParam(\ReflectionParameter $param)
    {
        $name = $param->getName();

        if (!isset($this->args['params'][$name]) && !$param->isOptional()) {
            $message = sprintf('You must define param "%s" for handler "%s"', $name, $this->args['class']);

            throw new \OutOfBoundsException($message);
        }

        if (!isset($this->args['params'][$name]) && $param->isOptional()) {
            return $param->getDefaultValue();
        }

        $value = $this->args['params'][$name];
        $param = is_string($value) && defined($value) ? constant($value) : $value;

        return $param;
    }
}
