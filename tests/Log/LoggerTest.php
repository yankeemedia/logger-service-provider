<?php

namespace Dafiti\Silex\Log;

class LoggerTest extends \PHPUnit_Framework_TestCase
{
    private $logger;

    public function setUp()
    {
        $this->logger = new Logger('test');
    }

    public function tearDown()
    {
        $this->logger = null;
    }

    /**
     * @covers Dafiti\Silex\Log\Logger::translateLevel
     * @expectedException InvalidArgumentException
     */
    public function testShouldThrowExceptionWhenLogLevelNotExists()
    {
        $this->logger->translateLevel('notify');
    }

    /**
     * @covers Dafiti\Silex\Log\Logger::translateLevel
     */
    public function testShouldTranslateLogLevel()
    {
        $result = $this->logger->translateLevel('debug');

        $this->assertEquals(Logger::DEBUG, $result);
    }

    /**
     * @covers Dafiti\Silex\Log\Logger::translateLevel
     */
     public function testReturnSameLevelWhenNameIsInteger()
     {
        $level = Logger::INFO;
        $result = $this->logger->translateLevel($level);

        $this->assertEquals($level, $result);
     }

    /**
     * @covers Dafiti\Silex\Log\Logger::log
     */
    public function testShouldLog()
    {
        $handler = $this->getMock('Monolog\Handler\NullHandler', array('handle'));
        $handler->expects($this->once())
            ->method('handle');

        $this->logger->pushHandler($handler);
        $this->assertTrue($this->logger->log('test'));
    }
}
