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
        $this->app->register(new LoggerServiceProvider());

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

        $this->app->register(new LoggerServiceProvider(), $params);

        $this->assertInstanceOf('\Dafiti\Silex\Log\Collection', $this->app['logger']);
        $this->assertEquals($params['logger.log_folder'], $this->app['logger.log_folder']);
        $this->assertEquals($params['logger.level'], $this->app['logger.level']);
    }

    /**
     * @covers Dafiti\Silex\LoggerServiceProvider::register
     */
    public function testShouldCreateLogger()
    {
        $this->app->register(new LoggerServiceProvider());

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
        $this->app->register(new LoggerServiceProvider());

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
        $this->app->register(new LoggerServiceProvider(), [
            'logger.log_folder' => 'data/logs'
        ]);

        $processLogger = $this->app['logger.create']('process');
        $workerLogger  = $this->app['logger.create']('worker', 'warning');

        $this->assertCount(2, $this->app['logger']);
        $this->assertTrue($this->app['logger']->has('process'));
        $this->assertTrue($this->app['logger']->has('worker'));
        $this->assertSame($processLogger, $this->app['logger']->get('process'));
        $this->assertSame($workerLogger, $this->app['logger']->get('worker'));
    }

    /**
     * @covers Dafiti\Silex\LoggerServiceProvider::register
     */
    public function testShouldCreateWithProcessor()
    {
        $this->app->register(new LoggerServiceProvider());

        $worker = $this->app['logger.create']('worker', 'info', [], [
            new Processor\UidProcessor()
        ]);

        $this->assertCount(1, $worker->getProcessors());
    }
}
