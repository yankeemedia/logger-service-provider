<?php

namespace Dafiti\Silex\Log;

class Logger extends \Monolog\Logger
{
    public function log($message, $level = self::DEBUG, array $context = [])
    {
        return parent::log($level, $message, $context);
    }

    public function translateLevel($name)
    {
        if (is_int($name)) {
            return $name;
        }

        $levels = $this->getLevels();
        $upper = strtoupper($name);

        if (!isset($levels[$upper])) {
            throw new \InvalidArgumentException("Provided logging level '$name' does not exist. Must be a valid monolog logging level.");
        }

        return $levels[$upper];
    }
}
