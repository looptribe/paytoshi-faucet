<?php

use Looptribe\Paytoshi\Model\SetupDiagnostics;

class SetupDiagnosticsTest extends PHPUnit_Framework_TestCase
{
    public function testRequiresSetupShouldReturnFalse()
    {
        $repository = $this->getMockBuilder('Looptribe\Paytoshi\Model\SettingsRepository')
            ->disableOriginalConstructor()
            ->getMock();
        $repository
            ->method('get')
            ->with('password')
            ->willReturn('fakepasswordhash');
        $sut = new SetupDiagnostics($repository);
        $result = $sut->requiresSetup();
        $this->assertFalse($result);
    }

    public function testRequiresSetupShouldReturnTrueIfPasswordNotSet()
    {
        $repository = $this->getMockBuilder('Looptribe\Paytoshi\Model\SettingsRepository')
            ->disableOriginalConstructor()
            ->getMock();
        $repository
            ->method('get')
            ->with('password')
            ->willReturn(null);
        $sut = new SetupDiagnostics($repository);
        $result = $sut->requiresSetup();
        $this->assertTrue($result);
    }

    public function testRequiresSetupShouldReturnTrueIfAnExceptionOccurs()
    {
        $repository = $this->getMockBuilder('Looptribe\Paytoshi\Model\SettingsRepository')
            ->disableOriginalConstructor()
            ->getMock();
        $repository
            ->method('get')
            ->with('password')
            ->willThrowException(new \RuntimeException());
        $sut = new SetupDiagnostics($repository);
        $result = $sut->requiresSetup();
        $this->assertTrue($result);
    }
}
