<?php

namespace Dafiti\Silex\Log\Factory;

use Dafiti\Silex\Log\Logger;

abstract class AbstractFactory
{
    protected $args = [];

    abstract public function __invoke(array $args);

    protected function getParams(\ReflectionMethod $method)
    {
        $params = [];

        foreach ($method->getParameters() as $param) {
            $name = $param->getName();
            $params[$name] = $this->getParam($param);
        }

        return $params;
    }

    protected function getParam(\ReflectionParameter $param)
    {
        $name = $param->getName();

        if (!isset($this->args['params'][$name]) && !$param->isOptional()) {
            $message = sprintf('You must define param "%s" for "%s"', $name, $this->args['class']);

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
