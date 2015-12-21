<?php

namespace Looptribe\Paytoshi\Tests\Model;

use Looptribe\Paytoshi\Model\SetupDiagnostics;

class SetupDiagnosticsTest extends \PHPUnit_Framework_TestCase
{
    public function testRequiresSetupShouldReturnFalse()
    {
        $db = $this->getMockBuilder('Doctrine\DBAL\Connection')
            ->disableOriginalConstructor()
            ->getMock();
        $repository = $this->getMockBuilder('Looptribe\Paytoshi\Model\SettingsRepository')
            ->disableOriginalConstructor()
            ->getMock();
        $repository
            ->method('get')
            ->with('password')
            ->willReturn('fakepasswordhash');
        $sut = new SetupDiagnostics($db, $repository, '/path/to/config.yml');
        $result = $sut->requiresSetup();
        $this->assertFalse($result);
    }

    public function testRequiresSetupShouldReturnTrueIfPasswordNotSet()
    {
        $db = $this->getMockBuilder('Doctrine\DBAL\Connection')
            ->disableOriginalConstructor()
            ->getMock();
        $repository = $this->getMockBuilder('Looptribe\Paytoshi\Model\SettingsRepository')
            ->disableOriginalConstructor()
            ->getMock();
        $repository
            ->method('get')
            ->with('password')
            ->willReturn(null);
        $sut = new SetupDiagnostics($db, $repository, '/path/to/config.yml');
        $result = $sut->requiresSetup();
        $this->assertTrue($result);
    }

    public function testRequiresSetupShouldReturnTrueIfAnExceptionOccurs()
    {
        $db = $this->getMockBuilder('Doctrine\DBAL\Connection')
            ->disableOriginalConstructor()
            ->getMock();
        $repository = $this->getMockBuilder('Looptribe\Paytoshi\Model\SettingsRepository')
            ->disableOriginalConstructor()
            ->getMock();
        $repository
            ->method('get')
            ->with('password')
            ->willThrowException(new \RuntimeException());
        $sut = new SetupDiagnostics($db, $repository, '/path/to/config.yml');
        $result = $sut->requiresSetup();
        $this->assertTrue($result);
    }

    public function testIsFileWritableShouldReturnFalseForNonExistingFile()
    {
        $db = $this->getMockBuilder('Doctrine\DBAL\Connection')
            ->disableOriginalConstructor()
            ->getMock();
        $repository = $this->getMockBuilder('Looptribe\Paytoshi\Model\SettingsRepository')
            ->disableOriginalConstructor()
            ->getMock();

        $sut = new SetupDiagnostics($db, $repository, 'thisfileshouldnotexist.yml');
        $result = $sut->isConfigWritable();
        $this->assertfalse($result);
    }
}
