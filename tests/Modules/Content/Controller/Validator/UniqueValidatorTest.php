<?php
namespace Test\Modules\Content\Controller\Validator;

use Test\Mock\MockTest;
use Test\Init\InitEntityController;
use Content\Controller\Validator\UniqueValidator;

class UniqueValidatorTest extends \PHPUnit_Framework_TestCase
{

    private $_uniqueValidator;

    public function setUp()
    {
        $anyEntity = \Mockery::mock('alias:Entity\AnyEntity');
        InitEntityController::mock('entityKey', $anyEntity);
        $controller = $this->getMockBuilder('\Content\Controller\Controller')
            ->disableOriginalConstructor()
            ->getMock();
        $controller->method('entity')->willReturn($anyEntity);
        $this->_uniqueValidator = new UniqueValidator($controller);
    }

    public function testSetQuery()
    {
        MockTest::callMockMethod($this->_uniqueValidator, 'setQuery');
        $_query = \PHPUnit_Framework_Assert::readAttribute($this->_uniqueValidator, '_query');
        $this->assertInstanceOf('Content\Query\Content', $_query);
    }

    public function testSetEntityKeyByEntityName()
    {
        MockTest::callMockMethod($this->_uniqueValidator, 'setEntityKey');
        $this->assertEquals(
            'entityKey',
            \PHPUnit_Framework_Assert::readAttribute($this->_uniqueValidator, 'entityKey')
        );
    }

    public function testDataNotExistsInDatabase()
    {
        MockTest::inject($this->_uniqueValidator, '_query', $this->mockQuery([]));
        $result = MockTest::callMockMethod(
            $this->_uniqueValidator,
            'dataNotExistInDatabase',
            ['column', 'value']
        );
        $this->assertTrue($result);
    }

    public function testDataExistsInDatabase()
    {
        MockTest::inject($this->_uniqueValidator, '_query', $this->mockQuery(['value']));
        $result = MockTest::callMockMethod(
            $this->_uniqueValidator,
            'dataNotExistInDatabase',
            ['column', 'value']
        );
        $this->assertFalse($result);
    }

    private function mockQuery(array $return)
    {
        $stub = \Mockery::mock('Content');
        $stub->shouldReceive([
            'setContent' => $stub,
            'findBy' => $stub,
            'force' => $stub,
            'paginate' => $stub,
            'getContent' => $return
        ]);
        return $stub;
    }
}
