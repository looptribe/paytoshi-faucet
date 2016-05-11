<?php

namespace Looptribe\Paytoshi\Tests\Setup;

use Looptribe\Paytoshi\Setup\Diagnostics;

class DiagnosticsTest extends \PHPUnit_Framework_TestCase
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
        $sut = new Diagnostics($repository, $connectionFactory, '/path/to/config.yml');
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
        $sut = new Diagnostics($repository, $connectionFactory, '/path/to/config.yml');
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
        $sut = new Diagnostics($repository, $connectionFactory, '/path/to/config.yml');
        $result = $sut->requiresSetup();
        $this->assertTrue($result);
    }

    public function testIsFileWritableShouldReturnFalseForNonExistingFile()
    {
        $connectionFactory = $this->getMock('Looptribe\Paytoshi\Model\ConnectionFactory');
        $repository = $this->getMockBuilder('Looptribe\Paytoshi\Model\SettingsRepository')
            ->disableOriginalConstructor()
            ->getMock();

        $sut = new Diagnostics($repository, $connectionFactory, 'thisfileshouldnotexist.yml');
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

        $sut = new Diagnostics($repository, $connectionFactory, '/path/to/config.yml');
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

        $sut = new Diagnostics($repository, $connectionFactory, '/path/to/config.yml');

        $this->setExpectedException('Doctrine\DBAL\ConnectionException');

        $sut->checkDatabase(array());
    }

    public function testCheckRewrite1()
    {
        $connectionFactory = $this->getMock('Looptribe\Paytoshi\Model\ConnectionFactory');
        $repository = $this->getMockBuilder('Looptribe\Paytoshi\Model\SettingsRepository')
            ->disableOriginalConstructor()
            ->getMock();

        $sut = new Diagnostics($repository, $connectionFactory, '/path/to/config.yml');

        $result = $sut->checkRewrite('http://localhost/path/to/faucet/setup/rewrite.json');
        $this->assertTrue($result);
    }

    public function testCheckRewrite2()
    {
        $connectionFactory = $this->getMock('Looptribe\Paytoshi\Model\ConnectionFactory');
        $repository = $this->getMockBuilder('Looptribe\Paytoshi\Model\SettingsRepository')
            ->disableOriginalConstructor()
            ->getMock();

        $sut = new Diagnostics($repository, $connectionFactory, '/path/to/config.yml');

        $result = $sut->checkRewrite('http://localhost/path/to/faucet/index.php/setup/rewrite.json');
        $this->assertFalse($result);
    }

    public function testCheckRewrite3()
    {
        $connectionFactory = $this->getMock('Looptribe\Paytoshi\Model\ConnectionFactory');
        $repository = $this->getMockBuilder('Looptribe\Paytoshi\Model\SettingsRepository')
            ->disableOriginalConstructor()
            ->getMock();

        $sut = new Diagnostics($repository, $connectionFactory, '/path/to/config.yml');

        $result = $sut->checkRewrite('/setup/rewrite.json');
        $this->assertTrue($result);
    }
}
