<?php

namespace Dafiti\Silex\Log\Factory;

class ProcessorTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @covers Dafiti\Silex\Log\Factory\Processor
     */
    public function testShouldIsAnInstanceOfAbstractFactory()
    {
        $this->assertInstanceOf('\Dafiti\Silex\Log\Factory\AbstractFactory', new Processor());
    }

    /**
     * @covers Dafiti\Silex\Log\Factory\Processor::__invoke
     *
     * @expectedException OutOfBoundsException
     * @expectedExceptionMessage You should define key class to create a log processor
     */
    public function testShouldThrowExceptionWhenCreateProcessorWithoutClass()
    {
        $factory = new Processor();
        $factory([]);
    }

    /**
     * @covers Dafiti\Silex\Log\Factory\Processor
     * @covers Dafiti\Silex\Log\Factory\AbstractFactory
     */
    public function testShouldCreateHandlerWithAllParameters()
    {
        $data = [
            'class'  => '\Monolog\Processor\GitProcessor',
            'params' => [
                'level' => 'debug',
            ]
        ];

        $factory = new Processor();

        $processor = $factory($data);

        $this->assertInstanceOf('\Monolog\Processor\GitProcessor', $processor);
    }
}