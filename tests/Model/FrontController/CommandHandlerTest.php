<?php
namespace Test\Model\FrontController;

use FrontController\CommandHandler;
use Ignaszak\Registry\RegistryFactory;

class CommandHandlerTest extends \PHPUnit_Framework_TestCase
{

    private $_commandHandler;

    public function setUp()
    {
        $this->_commandHandler = new CommandHandler();
    }

    public function testConstructor()
    {
        $_base = \PHPUnit_Framework_Assert::readAttribute($this->_commandHandler, '_base');
        $this->assertInstanceOf('ReflectionClass', $_base);
        $this->assertEquals($_base->getName(), 'FrontController\Controller');
    }

    public function testLoadDefaultController()
    {
        $this->mockView();
        $stub = $this->getMock('App\Resource\Route');
        $getCommand = $this->_commandHandler->getCommand($stub);
        $this->assertTrue($getCommand);
    }

    public function testSetControllerClassInstance()
    {
        $this->mockView();
        $stubRoute = $this->getMock('App\Resource\Route');
        $stubRoute->expects($this->any())
            ->method('__get')
            ->will($this->returnValue('Controller\DefaultController'));
        $getCommand = $this->_commandHandler->getCommand($stubRoute);
        $this->assertTrue($getCommand);
    }

    private function mockView()
    {
        $_view = \Mockery::mock('alias:View\View');
        $_view->shouldReceive('addView');
        RegistryFactory::start()->set('view', $_view);
    }
}