<?php

namespace Dafiti\Silex\Log;

class Logger extends \Monolog\Logger
{
    public function log($message, $level = self::DEBUG, array $context = [])
    {
        $level = $this->translateLevel($level);

        return parent::log($level, $message, $context);
    }

    public static function translateLevel($name)
    {
        if (is_int($name)) {
            return $name;
        }

        $levels = self::getLevels();
        $upper = strtoupper($name);

        if (!isset($levels[$upper])) {
            throw new \InvalidArgumentException("Provided logging level '$name' does not exist. Must be a valid monolog logging level.");
        }

        return $levels[$upper];
    }
}
