<?php

namespace Looptribe\Paytoshi\Tests\Model;

use Looptribe\Paytoshi\Model\Configurator;

class ConfiguratorTest extends \PHPUnit_Framework_TestCase
{
    public function testSetup()
    {
        $connection = $this->getMockBuilder('Doctrine\DBAL\Connection')
            ->disableOriginalConstructor()
            ->getMock();
        $passwordGenerator = $this->getMock('Looptribe\Paytoshi\Security\PasswordGeneratorInterface');
        $saltGenerator = $this->getMock('Looptribe\Paytoshi\Security\SaltGeneratorInterface');
        $passwordEncoder = $this->getMock('Symfony\Component\Security\Core\Encoder\PasswordEncoderInterface');
        $sut = new Configurator($connection, $passwordGenerator, $saltGenerator, $passwordEncoder,
            __DIR__ . '/../../data/setup.sql');

        $connection
            ->expects($this->once())
            ->method('executeQuery');
        $passwordGenerator
            ->expects($this->once())
            ->method('generate')
            ->willReturn('test');
        $saltGenerator
            ->expects($this->once())
            ->method('generate')
            ->willReturn('salt');
        $passwordEncoder
            ->expects($this->once())
            ->method('encodePassword')
            ->with('test', 'salt')
            ->willReturn('passwordhash');

        $result = $sut->setup();

        $this->assertEquals('test', $result['password']);
    }

    public function testSetupShouldFailForMissingSqlFile()
    {
        $connection = $this->getMockBuilder('Doctrine\DBAL\Connection')
            ->disableOriginalConstructor()
            ->getMock();
        $passwordGenerator = $this->getMock('Looptribe\Paytoshi\Security\PasswordGeneratorInterface');
        $saltGenerator = $this->getMock('Looptribe\Paytoshi\Security\SaltGeneratorInterface');
        $passwordEncoder = $this->getMock('Symfony\Component\Security\Core\Encoder\PasswordEncoderInterface');
        $sut = new Configurator($connection, $passwordGenerator, $saltGenerator, $passwordEncoder, 'fakepath.sql');

        $connection
            ->expects($this->never())
            ->method('executeQuery');
        $passwordGenerator
            ->expects($this->once())
            ->method('generate')
            ->willReturn('test');
        $passwordEncoder
            ->expects($this->once())
            ->method('encodePassword')
            ->willReturn('passwordhash');

        $this->setExpectedException('RuntimeException');
        $sut->setup();
    }
}
