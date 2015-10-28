<?php

namespace Dafiti\Silex\Log\Factory;

class Processor extends AbstractFactory
{
    public function __invoke(array $args)
    {
        $this->args = $args;

        if (!isset($this->args['class']) || empty($this->args['class'])) {
            throw new \OutOfBoundsException('You should define key class to create a log processor');
        }

        $class = new \ReflectionClass($args['class']);
        $params = [];

        if ($constructor = $class->getConstructor()) {
            $params = $this->getParams($constructor);
        }

        return $class->newInstanceArgs($params);
    }
}
