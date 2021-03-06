<?php
namespace Test\Model\App;

use App\Valid;
use Test\Mock\MockTest;
use Test\Mock\MockConf;

class ValidTest extends \PHPUnit_Framework_TestCase
{

    /**
     *
     * @var \App\Valid
     */
    private $valid;

    public static function setUpBeforeClass()
    {
        MockConf::setConstants();
    }

    public function setUp()
    {
        $this->valid = new Valid($this->mockMessage());
    }

    public function testConstructor()
    {
        $message = \PHPUnit_Framework_Assert::readAttribute(
            $this->valid,
            'message'
        );
        $this->assertInstanceOf('App\Message', $message);

        $check = \PHPUnit_Framework_Assert::readAttribute(
            $this->valid,
            'check'
        );
        $this->assertInstanceOf('App\Conf\Check', $check);
    }

    public function testAdd()
    {
        MockTest::callMockMethod($this->valid, 'add');
        $validArray = \PHPUnit_Framework_Assert::readAttribute(
            $this->valid,
            'validArray'
        );
        $this->assertNotEmpty($validArray);
    }

    public function testValidNoExistingFileORFolder()
    {
        $this->mockValidArray([
            ['NoExistingFileOrFolderToCheck', "r"]
        ]);
        $stub = $this->getMockBuilder('App\Conf\Check')->getMock();
        $stub->method('add');
        $stub->expects($this->once())->method('exists')->willReturn(false);
        $stub->expects($this->never())->method('isReadable');
        $stub->expects($this->never())->method('isWritable');
        $this->mockCheck($stub);
        $this->valid->valid();
    }

    public function testValidReadOnly()
    {
        $this->mockValidArray([
            ['AnyFileOrFolderToCheck', "r"]
        ]);
        $stub = $this->getMockBuilder('App\Conf\Check')->getMock();
        $stub->method('add');
        $stub->expects($this->once())->method('exists')->willReturn(true);
        $stub->expects($this->once())->method('isReadable');
        $stub->expects($this->never())->method('isWritable');
        $this->mockCheck($stub);
        $this->valid->valid();
    }

    public function testValidReadAndWrite()
    {
        $this->mockValidArray([
            ['AnyFileOrFolderToCheck', "r+"]
        ]);
        $stub = $this->getMockBuilder('App\Conf\Check')->getMock();
        $stub->method('add');
        $stub->expects($this->once())->method('exists')->willReturn(true);
        $stub->expects($this->once())->method('isReadable');
        $stub->expects($this->once())->method('isWritable');
        $this->mockCheck($stub);
        $this->valid->valid();
    }

    public function testModRewriteDisable()
    {
        $this->assertFalse(MockTest::callMockMethod(
            $this->valid,
            'isModRewriteEnabled'
        ));
    }

    public function testSetModRewriteMessage()
    {
        $stub = $this->mockMessage();
        $stub->expects($this->once())->method('catchMessage');
        $_valid = new Valid($stub);
        $_valid->validModRewrite();
    }

    public function testModRewriteEnable()
    {
        putenv("HTTP_MOD_REWRITE=On");
        $this->assertTrue(MockTest::callMockMethod(
            $this->valid,
            'isModRewriteEnabled'
        ));
    }

    public function testDontSetModRewriteMessage()
    {
        $stub = $this->mockMessage();
        $stub->expects($this->never())->method('catchMessage');
        $_valid = new Valid($stub);
        $_valid->validModRewrite();
    }

    private function mockCheck($stub)
    {
        MockTest::inject($this->valid, 'check', $stub);
    }

    private function mockValidArray(array $array)
    {
        MockTest::inject($this->valid, 'validArray', $array);
    }

    private function mockMessage(): \App\Message
    {
        $stub = $this->getMockBuilder('\App\Message')
            ->getMock();
        $stub->method('catch');
        return $stub;
    }
}
