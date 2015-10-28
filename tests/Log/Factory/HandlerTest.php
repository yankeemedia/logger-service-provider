<?php

namespace Dafiti\Silex\Log\Factory;

class HandlerTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @covers Dafiti\Silex\Log\Factory\Handler
     */
    public function testShouldIsAnInstanceOfAbstractFactory()
    {
        $this->assertInstanceOf('\Dafiti\Silex\Log\Factory\AbstractFactory', new Handler());
    }

    /**
     * @covers Dafiti\Silex\Log\Factory\Handler::__invoke
     *
     * @expectedException OutOfBoundsException
     * @expectedExceptionMessage You should define key class to create a log handler
     */
    public function testShouldThrowExceptionWhenCreateHandlerWithoutClass()
    {
        $factory = new Handler();
        $factory([]);
    }

    /**
     * @covers Dafiti\Silex\Log\Factory\Handler
     * @covers Dafiti\Silex\Log\Factory\AbstractFactory
     *
     * @expectedException OutOfBoundsException
     * @expectedExceptionMessage You must define param "token" for "\Monolog\Handler\HipChatHandler"
     */
    public function testShouldThrowExceptionWhenCreateHandlerWithoutRequiredParams()
    {
        $handler = [
            'class'  => '\Monolog\Handler\HipChatHandler',
            'params' => []
        ];

        $factory = new Handler();
        $factory($handler);
    }

    /**
     * @covers Dafiti\Silex\Log\Factory\Handler
     * @covers Dafiti\Silex\Log\Factory\AbstractFactory
     */
    public function testShouldCreateHandlerWithRequiredParams()
    {
        $handler = [
            'class'  => '\Monolog\Handler\StreamHandler',
            'params' => [
                'stream' => '/tmp/handler.log'
            ]
        ];

        $factory = new Handler();
        $factory($handler);
    }

    /**
     * @covers Dafiti\Silex\Log\Factory\Handler
     * @covers Dafiti\Silex\Log\Factory\AbstractFactory
     */
    public function testShouldCreateHandlerWithAllParameters()
    {
        $data = [
            'class'  => '\Monolog\Handler\StreamHandler',
            'params' => [
                'stream'         => '/tmp/handler.log',
                'level'          => 'debug',
                'bubble'         => false,
                'filePermission' => null,
                'useLocking'     => true
            ]
        ];

        $factory = new Handler();

        $handler = $factory($data);

        $this->assertInstanceOf('\Monolog\Handler\StreamHandler', $handler);
    }
}
