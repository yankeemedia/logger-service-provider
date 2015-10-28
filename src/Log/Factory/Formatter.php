<?php

namespace Dafiti\Silex\Log\Factory;

use Dafiti\Silex\Log\Factory\AbstractFactory;
use Dafiti\Silex\Log\Logger;

class Formatter extends AbstractFactory
{
     public function __invoke(array $args)
     {
         $this->args = $args;

        if (!isset($this->args['class']) || empty($this->args['class'])) {
            throw new \OutOfBoundsException('You should define key class to create a log formatter');
        }

        $class = new \ReflectionClass($args['class']);
        $params = [];

        if ($constructor = $class->getConstructor()) {
            $params = $this->getParams($constructor);
        }

        $object = $class->newInstanceArgs($params);

        if (!$object instanceof \Monolog\Formatter\FormatterInterface) {
            throw new \InvalidArgumentException('Class must be instance of "\Monolog\Formatter\FormatterInterface"');
        }

        return $object;
    }
}