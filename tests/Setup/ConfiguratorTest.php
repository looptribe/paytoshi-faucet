<?php

namespace Looptribe\Paytoshi\Tests\Setup;

use Looptribe\Paytoshi\Setup\Configurator;
use org\bovigo\vfs\vfsStream;
use org\bovigo\vfs\vfsStreamDirectory;

class ConfiguratorTest extends \PHPUnit_Framework_TestCase
{
    /** @var vfsStreamDirectory */
    private $vfs;

    public function setUp()
    {
        $this->vfs = vfsStream::setup('test-config');
    }

    public function testSetup()
    {
        $connection = $this->getMockBuilder('Doctrine\DBAL\Connection')
            ->disableOriginalConstructor()
            ->getMock();
        $passwordGenerator = $this->getMock('Looptribe\Paytoshi\Security\PasswordGeneratorInterface');
        $saltGenerator = $this->getMock('Looptribe\Paytoshi\Security\SaltGeneratorInterface');
        $passwordEncoder = $this->getMock('Symfony\Component\Security\Core\Encoder\PasswordEncoderInterface');
        $sut = new Configurator($connection, $passwordGenerator, $saltGenerator, $passwordEncoder,
            __DIR__ . '/../../data/setup.sql', __DIR__ . '/fake/path/to/config.yml');

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
        $sut = new Configurator($connection, $passwordGenerator, $saltGenerator, $passwordEncoder, 'fakepath.sql',
            __DIR__ . '/fake/path/to/config.yml');

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

    public function testSaveConfig()
    {
        $connection = $this->getMockBuilder('Doctrine\DBAL\Connection')
            ->disableOriginalConstructor()
            ->getMock();
        $passwordGenerator = $this->getMock('Looptribe\Paytoshi\Security\PasswordGeneratorInterface');
        $saltGenerator = $this->getMock('Looptribe\Paytoshi\Security\SaltGeneratorInterface');
        $passwordEncoder = $this->getMock('Symfony\Component\Security\Core\Encoder\PasswordEncoderInterface');
        $sut = new Configurator($connection, $passwordGenerator, $saltGenerator, $passwordEncoder, 'fakepath.sql',
            vfsStream::url('test-config/config.yml'));

        $config = array(
            'database' => array(
                'name' => 'fakedb',
                'host' => 'localhost',
                'username' => 'root',
                'password' => 'pass',
            )
        );

        $this->assertFalse($this->vfs->hasChild('config.yml'));
        $sut->saveConfig($config);
        $this->assertTrue($this->vfs->hasChild('config.yml'));
    }

    public function testSaveConfigShouldFailIfNotWritable()
    {
        $connection = $this->getMockBuilder('Doctrine\DBAL\Connection')
            ->disableOriginalConstructor()
            ->getMock();
        $passwordGenerator = $this->getMock('Looptribe\Paytoshi\Security\PasswordGeneratorInterface');
        $saltGenerator = $this->getMock('Looptribe\Paytoshi\Security\SaltGeneratorInterface');
        $passwordEncoder = $this->getMock('Symfony\Component\Security\Core\Encoder\PasswordEncoderInterface');
        $sut = new Configurator($connection, $passwordGenerator, $saltGenerator, $passwordEncoder, 'fakepath.sql',
            vfsStream::url('test-config/config.yml'));

        $config = array(
            'database' => array(
                'name' => 'fakedb',
                'host' => 'localhost',
                'username' => 'root',
                'password' => 'pass',
            )
        );

        $this->vfs->chmod(0555);

        $this->setExpectedException('\RuntimeException');
        $sut->saveConfig($config);
    }
}
