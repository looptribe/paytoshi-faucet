<?php

namespace Looptribe\Paytoshi\Tests\Templating;

use Looptribe\Paytoshi\Templating\LocalThemeProvider;
use org\bovigo\vfs\vfsStream;
use org\bovigo\vfs\vfsStreamDirectory;

class LocalThemeProviderTest extends \PHPUnit_Framework_TestCase
{
    /** @var  vfsStreamDirectory */
    private $vfs;

    const THEME_DIRECTORY = 'test-themes';

    public function setUp()
    {
        $this->vfs = vfsStream::setup(self::THEME_DIRECTORY);
    }

    public function testGetThemes1()
    {
        $settingsRepository = $this->getMockBuilder('Looptribe\Paytoshi\Model\SettingsRepository')
            ->disableOriginalConstructor()
            ->getMock();
        $defaultTheme = 'default';

        $sut = new LocalThemeProvider($settingsRepository, vfsStream::url(self::THEME_DIRECTORY), $defaultTheme);
        $themes = $sut->getList();
        $this->assertInternalType('array', $themes);
        $this->assertEquals(0, count($themes));
    }

    public function testGetThemes2()
    {
        $settingsRepository = $this->getMockBuilder('Looptribe\Paytoshi\Model\SettingsRepository')
            ->disableOriginalConstructor()
            ->getMock();
        $defaultTheme = 'default';

        $structure = array(
            'theme1' => array(),
            'theme2' => array(),
            'file1' => 'content1',
            'file2' => 'content2'
        );
        vfsStream::create($structure);

        $sut = new LocalThemeProvider($settingsRepository, vfsStream::url(self::THEME_DIRECTORY), $defaultTheme);
        $themes = $sut->getList();
        $this->assertInternalType('array', $themes);
        $this->assertEquals(2, count($themes));
        $this->assertEquals('theme1', $themes[0]);
        $this->assertEquals('theme2', $themes[1]);
    }

    public function testGetCurrent()
    {
        $templatePath = '.';
        $settingsRepository = $this->getMockBuilder('Looptribe\Paytoshi\Model\SettingsRepository')
            ->disableOriginalConstructor()
            ->getMock();
        $settingsRepository->method('get')
            ->willReturn('theme1');
        $defaultTheme = 'default';

        $sut = new LocalThemeProvider($settingsRepository, vfsStream::url(self::THEME_DIRECTORY), $defaultTheme);
        $currentTheme = $sut->getCurrent();
        $this->assertEquals('theme1', $currentTheme);
    }

    public function testGetTemplate1()
    {
        $templatePath = '.';
        $settingsRepository = $this->getMockBuilder('Looptribe\Paytoshi\Model\SettingsRepository')
            ->disableOriginalConstructor()
            ->getMock();
        $settingsRepository->method('get')
            ->willReturn('theme1');
        $defaultTheme = 'default';
        $structure = array(
            'theme1' => array(
                'template.html.twig' => 'template'
            ),
        );
        vfsStream::create($structure);

        $sut = new LocalThemeProvider($settingsRepository, vfsStream::url(self::THEME_DIRECTORY), $defaultTheme);
        $templateString = $sut->getTemplate('template.html.twig');
        $this->assertEquals('theme1/template.html.twig', $templateString);
        $this->assertTrue($this->vfs->hasChild('theme1/template.html.twig'));
    }

    public function testGetTemplate2()
    {
        $settingsRepository = $this->getMockBuilder('Looptribe\Paytoshi\Model\SettingsRepository')
            ->disableOriginalConstructor()
            ->getMock();
        $settingsRepository->method('get')
            ->willReturn('theme1');
        $defaultTheme = 'default';
        $structure = array(
            'theme1' => array()
        );
        vfsStream::create($structure);

        $sut = new LocalThemeProvider($settingsRepository, vfsStream::url(self::THEME_DIRECTORY), $defaultTheme);
        $this->setExpectedException('\Exception');
        $templateString = $sut->getTemplate('template.html.twig');
        $this->assertFalse($this->vfs->hasChild('theme1/template.html.twig'));
    }

    public function testGetTemplate3()
    {
        $settingsRepository = $this->getMockBuilder('Looptribe\Paytoshi\Model\SettingsRepository')
            ->disableOriginalConstructor()
            ->getMock();
        $settingsRepository->method('get')
            ->willReturn('theme1');
        $defaultTheme = 'default';
        $structure = array(
            'theme1' => array(
                'template.html.twig' => array()
            )
        );
        vfsStream::create($structure);

        $sut = new LocalThemeProvider($settingsRepository, vfsStream::url(self::THEME_DIRECTORY), $defaultTheme);
        $this->setExpectedException('\Exception');
        $templateString = $sut->getTemplate('template.html.twig');
        $this->assertTrue($this->vfs->hasChild('theme1/template.html.twig'));
        $this->assertFalse(is_file(vfsStream::url(self::THEME_DIRECTORY . '/theme1/template.html.twig')));
    }
}
