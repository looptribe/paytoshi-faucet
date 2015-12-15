<?php

namespace Looptribe\Paytoshi\Tests\Model;

use Looptribe\Paytoshi\Model\SettingsRepository;

class SettingsRepositoryTest extends \PHPUnit_Framework_TestCase
{
    /** @var \PHPUnit_Framework_MockObject_MockObject */
    private $connection;

    /** @var SettingsRepository */
    private $sut;

    public function setUp()
    {
        $this->connection = $this->getMockBuilder('Doctrine\DBAL\Connection')
            ->disableOriginalConstructor()
            ->getMock();
        $this->sut = new SettingsRepository($this->connection);
    }

    public function testGet1()
    {
        $this->connection
            ->expects($this->once())
            ->method('fetchAll')
            ->willReturn(array());

        $result = $this->sut->get('key1');

        $this->assertNull($result);
    }

    public function testGet2()
    {
        $this->connection
            ->expects($this->once())
            ->method('fetchAll')
            ->willReturn(array(
                array('name' => 'key1', 'value' => 'testvalue1'),
                array('name' => 'key2', 'value' => 'testvalue2'),
                array('name' => 'key3', 'value' => 'testvalue3'),
                array('name' => 'key4', 'value' => 'testvalue4'),
            ));

        $result = $this->sut->get('key1');

        $this->assertEquals('testvalue1', $result);
    }

    public function testGet3()
    {
        $this->connection
            ->expects($this->once())
            ->method('fetchAll')
            ->willReturn(array(
                array('name' => 'key1', 'value' => 'testvalue1'),
                array('name' => 'key2', 'value' => 'testvalue2'),
                array('name' => 'key3', 'value' => 'testvalue3'),
                array('name' => 'key4', 'value' => 'testvalue4'),
            ));

        $result = $this->sut->get('key10');

        $this->assertNull($result);
    }

    public function testGet4()
    {
        $this->connection
            ->expects($this->once())
            ->method('fetchAll')
            ->willReturn(array(
                array('name' => 'key1', 'value' => 'testvalue1'),
                array('name' => 'key2', 'value' => 'testvalue2'),
                array('name' => 'key3', 'value' => 'testvalue3'),
                array('name' => 'key4', 'value' => 'testvalue4'),
            ));

        $result = $this->sut->get('key10', 'defaultValue');

        $this->assertEquals('defaultValue', $result);
    }

    public function testGet5()
    {
        $this->connection
            ->expects($this->once())
            ->method('fetchAll')
            ->willReturn(array(
                array('name' => 'key1', 'value' => 'testvalue1'),
                array('name' => 'key2', 'value' => 'testvalue2'),
                array('name' => 'key3', 'value' => 'testvalue3'),
                array('name' => 'key4', 'value' => 'testvalue4'),
            ));

        $result = $this->sut->get('key1', 'defaultValue');

        $this->assertEquals('testvalue1', $result);
    }
}
