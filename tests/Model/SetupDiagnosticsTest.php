<?php

namespace Looptribe\Paytoshi\Tests\Model;

use Looptribe\Paytoshi\Model\SetupDiagnostics;

class SetupDiagnosticsTest extends \PHPUnit_Framework_TestCase
{
    public function testRequiresSetupShouldReturnFalse()
    {
        $connectionFactory = $this->getMock('Looptribe\Paytoshi\Model\ConnectionFactory');
        $repository = $this->getMockBuilder('Looptribe\Paytoshi\Model\SettingsRepository')
            ->disableOriginalConstructor()
            ->getMock();
        $repository
            ->method('get')
            ->with('password')
            ->willReturn('fakepasswordhash');
        $sut = new SetupDiagnostics($repository, $connectionFactory, '/path/to/config.yml');
        $result = $sut->requiresSetup();
        $this->assertFalse($result);
    }

    public function testRequiresSetupShouldReturnTrueIfPasswordNotSet()
    {
        $connectionFactory = $this->getMock('Looptribe\Paytoshi\Model\ConnectionFactory');
        $repository = $this->getMockBuilder('Looptribe\Paytoshi\Model\SettingsRepository')
            ->disableOriginalConstructor()
            ->getMock();
        $repository
            ->method('get')
            ->with('password')
            ->willReturn(null);
        $sut = new SetupDiagnostics($repository, $connectionFactory, '/path/to/config.yml');
        $result = $sut->requiresSetup();
        $this->assertTrue($result);
    }

    public function testRequiresSetupShouldReturnTrueIfAnExceptionOccurs()
    {
        $connectionFactory = $this->getMock('Looptribe\Paytoshi\Model\ConnectionFactory');
        $repository = $this->getMockBuilder('Looptribe\Paytoshi\Model\SettingsRepository')
            ->disableOriginalConstructor()
            ->getMock();
        $repository
            ->method('get')
            ->with('password')
            ->willThrowException(new \RuntimeException());
        $sut = new SetupDiagnostics($repository, $connectionFactory, '/path/to/config.yml');
        $result = $sut->requiresSetup();
        $this->assertTrue($result);
    }

    public function testIsFileWritableShouldReturnFalseForNonExistingFile()
    {
        $connectionFactory = $this->getMock('Looptribe\Paytoshi\Model\ConnectionFactory');
        $repository = $this->getMockBuilder('Looptribe\Paytoshi\Model\SettingsRepository')
            ->disableOriginalConstructor()
            ->getMock();

        $sut = new SetupDiagnostics($repository, $connectionFactory, 'thisfileshouldnotexist.yml');
        $result = $sut->isConfigWritable();
        $this->assertfalse($result);
    }

    public function testCheckDatabase1()
    {
        $connectionFactory = $this->getMock('Looptribe\Paytoshi\Model\ConnectionFactory');
        $repository = $this->getMockBuilder('Looptribe\Paytoshi\Model\SettingsRepository')
            ->disableOriginalConstructor()
            ->getMock();

        $connection = $this->getMockBuilder('Doctrine\DBAL\Connection')
            ->disableOriginalConstructor()
            ->getMock();

        $schemaManager = $this->getMockBuilder('Doctrine\DBAL\Schema\AbstractSchemaManager')
            ->disableOriginalConstructor()
            ->getMock();

        $connectionFactory
            ->expects($this->once())
            ->method('create')
            ->willReturn($connection);

        $connection
            ->method('getSchemaManager')
            ->willReturn($schemaManager);
        $schemaManager
            ->expects($this->once())
            ->method('listTables')
            ->willReturn(array());

        $sut = new SetupDiagnostics($repository, $connectionFactory, '/path/to/config.yml');
        $sut->checkDatabase(array());
    }

    public function testCheckDatabase2()
    {
        $connectionFactory = $this->getMock('Looptribe\Paytoshi\Model\ConnectionFactory');
        $repository = $this->getMockBuilder('Looptribe\Paytoshi\Model\SettingsRepository')
            ->disableOriginalConstructor()
            ->getMock();

        $connection = $this->getMockBuilder('Doctrine\DBAL\Connection')
            ->disableOriginalConstructor()
            ->getMock();

        $schemaManager = $this->getMockBuilder('Doctrine\DBAL\Schema\AbstractSchemaManager')
            ->disableOriginalConstructor()
            ->getMock();

        $connectionFactory
            ->expects($this->once())
            ->method('create')
            ->willReturn($connection);

        $connection
            ->method('getSchemaManager')
            ->willReturn($schemaManager);
        $schemaManager
            ->expects($this->once())
            ->method('listTables')
            ->willThrowException(new \Doctrine\DBAL\ConnectionException());

        $sut = new SetupDiagnostics($repository, $connectionFactory, '/path/to/config.yml');

        $this->setExpectedException('Doctrine\DBAL\ConnectionException');

        $sut->checkDatabase(array());
    }
}
