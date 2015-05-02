<?php

namespace Dafiti\Silex;

use Monolog\Handler;
use Monolog\Processor;
use Silex\Application;

class LoggerServiceProviderTest extends \PHPUnit_Framework_TestCase
{
    private $app;

    public function setUp()
    {
        $this->app = new Application();
        $this->app->register(new LoggerServiceProvider());

        parent::setUp();
    }

    public function tearDown()
    {
        $this->app = null;
    }

    /**
     * @covers Dafiti\Silex\LoggerServiceProvider::register
     */
    public function testShouldRegister()
    {
        $this->assertInstanceOf('\Dafiti\Silex\Log\Collection', $this->app['logger']);
    }

    /**
     * @covers Dafiti\Silex\LoggerServiceProvider::register
     */
    public function testShouldRegisterWithParams()
    {
        $params = [
            'logger.log_folder' => 'data/logs/',
            'logger.level'      => 'debug'
        ];

        $app = new Application();

        $app->register(new LoggerServiceProvider(), $params);

        $this->assertInstanceOf('\Dafiti\Silex\Log\Collection', $app['logger']);
        $this->assertEquals($params['logger.log_folder'], $app['logger.log_folder']);
        $this->assertEquals($params['logger.level'], $app['logger.level']);
    }

    /**
     * @covers Dafiti\Silex\LoggerServiceProvider::register
     */
    public function testShouldCreateLogger()
    {
        $logger = $this->app['logger.create']('process');
        $handlers = $logger->getHandlers();

        $this->assertInstanceOf('\Dafiti\Silex\Log\Logger', $logger);
        $this->assertContainsOnlyInstancesOf('\Monolog\Handler\StreamHandler', $handlers);
        $this->assertEquals($logger::DEBUG, $handlers[0]->getLevel());
    }

    /**
     * @covers Dafiti\Silex\LoggerServiceProvider::register
     */
    public function testShouldCreateLoggerWithAnotherHandler()
    {
        $logger = $this->app['logger.create']('process', 'debug', [
            new Handler\FirePHPHandler(),
            new Handler\ErrorLogHandler(Handler\ErrorLogHandler::OPERATING_SYSTEM)
        ]);

        $this->assertCount(2, $logger->getHandlers());
    }

    /**
     * @covers Dafiti\Silex\LoggerServiceProvider::register
     */
    public function testShouldCreateMultipleLoggers()
    {
        $app = new Application();

        $app->register(new LoggerServiceProvider(), [
            'logger.log_folder' => 'data/logs'
        ]);

        $processLogger = $app['logger.create']('process');
        $workerLogger  = $app['logger.create']('worker', 'warning');

        $this->assertCount(2, $app['logger']);
        $this->assertTrue($app['logger']->has('process'));
        $this->assertTrue($app['logger']->has('worker'));
        $this->assertSame($processLogger, $app['logger']->get('process'));
        $this->assertSame($workerLogger, $app['logger']->get('worker'));
    }

    /**
     * @covers Dafiti\Silex\LoggerServiceProvider::register
     */
    public function testShouldCreateWithProcessor()
    {
        $worker = $this->app['logger.create']('worker', 'info', [], [
            new Processor\UidProcessor()
        ]);

        $this->assertCount(1, $worker->getProcessors());
    }

    /**
     * @covers Dafiti\Silex\LoggerServiceProvider::register
     *
     * @expectedException InvalidArgumentException
     * @expectedExceptionMessage Empty value is not allowed for loggers
     */
    public function testShouldThrowExceptionWhenFabricateWithWithoutLoggers()
    {
        $this->app['logger.factory']([]);
    }

    /**
     * @covers Dafiti\Silex\LoggerServiceProvider::register
     */
    public function testShouldFabricateLoggersWithoutHandlers()
    {
        $loggers = [
            'worker' => [
                'level' => 'info'
            ]
        ];

        $this->app['logger.factory']($loggers);

        $this->assertCount(1, $this->app['logger']);
        $this->assertCount(1, $this->app['logger']->worker->getHandlers());
        $this->assertInstanceOf('\Monolog\Handler\StreamHandler', $this->app['logger']->worker->getHandlers()[0]);
    }

    /**
     * @covers Dafiti\Silex\LoggerServiceProvider::register
     */
    public function testShouldFabricateLoggersWithDefaulLevelInHandlersWhenIsNotDefined()
    {
        $loggers = [
            'worker' => [
                'level' => 'warning',
                'handlers' => [
                    [
                        'class' => '\Monolog\Handler\StreamHandler',
                        'params' => [
                            'stream'         => '/tmp/test.log',
                            'bubble'         => true,
                            'filePermission' => null
                        ]
                    ]
                ]
            ]
        ];

        $this->app['logger.factory']($loggers);

        $this->assertCount(1, $this->app['logger']);
        $this->assertCount(1, $this->app['logger']->worker->getHandlers());
        $this->assertInstanceOf('\Monolog\Handler\StreamHandler', $this->app['logger']->worker->getHandlers()[0]);
    }

    /**
     * @covers Dafiti\Silex\LoggerServiceProvider::register
     */
    public function testShouldFabricateLoggers()
    {
        $loggers = [
            'worker' => [
                'level' => 'debug',
                'handlers' => [
                    [
                        'class' => '\Monolog\Handler\StreamHandler',
                        'params' => [
                            'stream'         => '/tmp/test.log',
                            'bubble'         => true,
                            'filePermission' => null
                        ]
                    ],
                    [
                        'class' => '\Monolog\Handler\SyslogHandler',
                        'params' => [
                            'ident'    => 'worker',
                            'facility' => LOG_USER
                        ]
                    ]
                ]
            ],
            'mail' => [
                'level' => 'debug',
                'handlers' => [
                    [
                        'class' => '\Monolog\Handler\StreamHandler',
                        'params' => [
                            'stream'         => '/tmp/test.log',
                            'bubble'         => true,
                            'filePermission' => null
                        ]
                    ]
                ]
            ]
        ];

        $this->app['logger.factory']($loggers);

        $this->assertCount(2, $this->app['logger']);
        $this->assertCount(2, $this->app['logger']->worker->getHandlers());
        $this->assertInstanceOf('\Monolog\Handler\StreamHandler', $this->app['logger']->worker->getHandlers()[1]);
        $this->assertCount(1, $this->app['logger']->mail->getHandlers());
        $this->assertInstanceOf('\Monolog\Handler\StreamHandler', $this->app['logger']->mail->getHandlers()[0]);
    }
}
