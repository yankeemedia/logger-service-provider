<?php

namespace Dafiti\Silex\Log\Factory;

use Dafiti\Silex\Log\Logger;

class Handler extends AbstractFactory
{
    public function __invoke(array $args)
    {
        $this->args = $args;

        if (!isset($this->args['class']) || empty($this->args['class'])) {
            throw new \OutOfBoundsException('You should define key class to create a log handler');
        }

        if (isset($this->args['params']['level'])) {
            $this->args['params']['level'] = Logger::translateLevel($this->args['params']['level']);
        }

        $class = new \ReflectionClass($this->args['class']);
        $params = $this->getParams($class->getConstructor());

        $object = $class->newInstanceArgs($params);

        if (isset($this->args['formatter'])) {
            $formatterFactory = new Formatter();
            $formatter = $formatterFactory($this->args['formatter']);

            $object->setFormatter($formatter);
        }

        return $object;
    }
}
