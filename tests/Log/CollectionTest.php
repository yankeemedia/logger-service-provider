<?php

namespace Dafiti\Silex\Log;

class CollectionTest extends \PHPUnit_Framework_TestCase
{
    private $loggers;

    public function setUp()
    {
        $this->loggers = new Collection();
    }

    public function tearDown()
    {
        $this->loggers = null;
    }

    /**
     * @covers Dafiti\Silex\Log\Collection::add
     * @covers Dafiti\Silex\Log\Collection::count
     */
    public function testShouldAddLogger()
    {
        $this->loggers->add(new Logger('test'));

        $this->assertCount(1, $this->loggers);
    }

    /**
     * @covers Dafiti\Silex\Log\Collection::has
     *
     * @expectedException InvalidArgumentException
     * @expectedExceptionMessage The argument of has method must be string
     */
    public function testShouldThrowExceptionWhenNotSetStringIntoHasMethod()
    {
        $this->loggers->has(['test']);
    }

    /**
     * @covers Dafiti\Silex\Log\Collection::has
     */
    public function testCheckIfLoggerExists()
    {
        $this->assertFalse($this->loggers->has('test'));
    }

    /**
     * @covers Dafiti\Silex\Log\Collection::get
     *
     * @expectedException InvalidArgumentException
     * @expectedExceptionMessage The argument of get method must be string
     */
    public function testShouldThrowExceptionWhenNotSetStringIntoGetMethod()
    {
        $this->loggers->get(['test']);
    }

    /**
     * @covers Dafiti\Silex\Log\Collection::get
     *
     * @expectedException OutOfBoundsException
     * @expectedExceptionMessage Logger test is not defined
     */
    public function testShouldThrowExceptionWhenGetInvalidLogger()
    {
        $this->loggers->get('test');
    }

    /**
     * @covers Dafiti\Silex\Log\Collection::get
     */
    public function testShouldGetLogger()
    {
        $logger = new Logger('test');

        $this->loggers->add($logger);

        $result = $this->loggers->get('test');

        $this->assertEquals($logger, $result);
    }

    /**
     * @covers Dafiti\Silex\Log\Collection::__get
     */
    public function testShouldGetLoggerUsingFluent()
    {
        $logger = new Logger('test');

        $this->loggers->add($logger);

        $result = $this->loggers->test;

        $this->assertEquals($logger, $result);
    }

    /**
     * @covers Dafiti\Silex\Log\Collection::getIterator
     */
    public function testShoulIterateOverLoggers()
    {
        $loggerTest = new Logger('test');

        $this->loggers->add($loggerTest);

        foreach ($this->loggers as $logger) {
            $this->assertEquals($loggerTest, $logger);
        }
    }
}
