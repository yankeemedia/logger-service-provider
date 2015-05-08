<?php

namespace Dafiti\Silex;

use Dafiti\Silex\Log\Factory;
use Dafiti\Silex\Log\Logger;
use Monolog\Handler\StreamHandler;
use Silex\Application;
use Silex\ServiceProviderInterface;

class LoggerServiceProvider implements ServiceProviderInterface
{
    public function register(Application $app)
    {
        $app['logger.class'] = '\Dafiti\Silex\Log\Logger';

        $app['logger.create'] = $app->protect(
            function ($name, $level = 'debug', array $handlers = [], array $processors = []) use ($app) {
                $logger = new $app['logger.class']($name);
                $level = $logger->translateLevel($level);

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

                $app['logger.manager']->add($logger);

                return $logger;
            }
        );

        $app['logger.handler'] = $app->protect(new Factory\Handler());

        $app['logger.factory'] = $app->protect(
            function (array $loggers) use ($app) {
                if (empty($loggers)) {
                    throw new \InvalidArgumentException('Empty value is not allowed for loggers');
                }

                foreach ($loggers as $name => $values) {
                    $level = 'debug';
                    $handlers = [];

                    if (isset($values['level'])) {
                        $level = $values['level'];
                    }

                    if (!isset($values['handlers'])) {
                        $values['handlers'] = [];
                    }

                    foreach ($values['handlers'] as $handler) {
                        if (!isset($handler['level'])) {
                            $handler['level'] = $level;
                        }

                        $handlers[] = $app['logger.handler']($handler);
                    }

                    $app['logger.create']($name, $level, $handlers);
                }
            }
        );

        $app['logger.manager'] = $app->share(
            function () {
                return new Log\Collection();
            }
        );

        $app['logger.bubble'] = true;
        $app['logger.permission'] = null;
        $app['logger.log_folder'] = null;
    }

    /**
     * @codeCoverageIgnore
     */
    public function boot(Application $app)
    {
    }
}
