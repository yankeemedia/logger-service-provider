<?php

namespace Dafiti\Silex\Log\Factory;

class FormatterTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @covers Dafiti\Silex\Log\Factory\Formatter
     */
    public function testShouldIsAnInstanceOfAbstractFactory()
    {
        $this->assertInstanceOf('\Dafiti\Silex\Log\Factory\AbstractFactory', new Formatter());
    }

    /**
     * @covers Dafiti\Silex\Log\Factory\Formatter::__invoke
     *
     * @expectedException OutOfBoundsException
     * @expectedExceptionMessage You should define key class to create a log formatter
     */
    public function testShouldThrowExceptionWhenCreateFormatterWithoutClass()
    {
        $factory = new Formatter();
        $factory([]);
    }

    /**
     * @covers Dafiti\Silex\Log\Factory\Formatter
     * @covers Dafiti\Silex\Log\Factory\AbstractFactory
     *
     * @expectedException OutOfBoundsException
     * @expectedExceptionMessage You must define param "applicationName" for "\Monolog\Formatter\LogstashFormatter"
     */
    public function testShouldThrowExceptionWhenCreateFormatterWithoutRequiredParams()
    {
        $formatter = [
            'class'  => '\Monolog\Formatter\LogstashFormatter',
            'params' => []
        ];

        $factory = new Formatter();
        $factory($formatter);
    }

    /**
     * @covers Dafiti\Silex\Log\Factory\Formatter::__invoke
     *
     * @expectedException InvalidArgumentException
     * @expectedExceptionMessage Class must be instance of "\Monolog\Formatter\FormatterInterface"
     */
    public function testShouldThrowExceptionWhenDefiniedClassIsNotFormatterInstance()
    {
        $formatter = [
            'class' => '\stdClass'
        ];

        $factory = new Formatter();
        $factory($formatter);
    }

    /**
     * @covers Dafiti\Silex\Log\Factory\Formatter
     * @covers Dafiti\Silex\Log\Factory\AbstractFactory
     */
    public function testShouldCreateFormatterWithRequiredParams()
    {
        $formatter = [
            'class'  => '\Monolog\Formatter\LogstashFormatter',
            'params' => [
                'applicationName' => 'dafiti'
            ]
        ];

        $factory = new Formatter();
        $this->assertInstanceOf('\Monolog\Formatter\FormatterInterface', $factory($formatter));
    }
}