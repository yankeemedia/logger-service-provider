<?php

namespace Dafiti\Silex\Log;

class Collection implements \Countable, \IteratorAggregate
{
    private $loggers = [];

    public function add(Logger $logger)
    {
        $this->loggers[$logger->getName()] = $logger;

        return $this;
    }

    public function count()
    {
        return count($this->loggers);
    }

    public function getIterator()
    {
        return new \ArrayIterator($this->loggers);
    }

    public function has($name)
    {
        if (!is_string($name)) {
            throw new \InvalidArgumentException('The argument of has method must be string');
        }

        return isset($this->loggers[$name]);
    }

    public function get($name)
    {
        if (!is_string($name)) {
            throw new \InvalidArgumentException('The argument of get method must be string');
        }

        if (!$this->has($name)) {
            $message = sprintf('Logger %s is not defined', $name);
            throw new \OutOfBoundsException($message);
        }

        return $this->loggers[$name];
    }

    public function __get($name)
    {
        return $this->get($name);
    }
}
