<?php

namespace Dafiti\Silex;

use Monolog\Handler\StreamHandler;
use Silex\Application;
use Silex\ServiceProviderInterface;

class LoggerServiceProvider implements ServiceProviderInterface
{
    public function register(Application $app)
    {
        $app['logger.class'] = '\Dafiti\Silex\Log\Logger';

        $app['logger.factory'] = $app->protect(
            function ($name, $level = 'debug', array $handlers = [], array $processors = []) use ($app) {
                $logger = new $app['logger.class']($name);
                $level  = $logger->translateLevel($level);

                if (empty($handlers)) {
                    $stream = sprintf('%s/%s.log', $app['logger.log_folder'], $name);
                    $handlers = [
                        new StreamHandler($stream, $level, $app['logger.bubble'], $app['logger.permission']),
                    ];
                }

                foreach ($handlers as $handler) {
                    $logger->pushHandler($handler);
                }

                foreach ($processors as $processor) {
                    $logger->pushProcessor($processor);
                }

                $app['logger']->add($logger);

                return $logger;
            }
        );

        $app['logger'] = $app->share(
            function () {
                return new Log\Collection();
            }
        );

        $app['logger.bubble'] = true;
        $app['logger.permission'] = null;
        $app['logger.log_folder'] = null;
    }

    public function boot(Application $app)
    {
    }
}
