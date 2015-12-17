<?php

namespace Looptribe\Paytoshi\Tests\Templating;

use Looptribe\Paytoshi\Templating\LocalThemeProvider;

class LocalThemeProviderTest extends \PHPUnit_Framework_TestCase
{
    const TEST_DIRECTORY = 'template_tests';

    public function setUp()
    {
        $this->rrmdir(self::TEST_DIRECTORY);
        mkdir(self::TEST_DIRECTORY);
        chdir(self::TEST_DIRECTORY);
    }

    public function testGetThemes1()
    {
        $templateDir = '.';
        $settingsRepository = $this->getMockBuilder('Looptribe\Paytoshi\Model\SettingsRepository')
            ->disableOriginalConstructor()
            ->getMock();

        $sut = new LocalThemeProvider($settingsRepository, $templateDir);
        $themes = $sut->getList();
        $this->assertInternalType('array', $themes);
        $this->assertEquals(0, count($themes));
    }

    public function testGetThemes2()
    {
        $templateDir = '.';
        $settingsRepository = $this->getMockBuilder('Looptribe\Paytoshi\Model\SettingsRepository')
            ->disableOriginalConstructor()
            ->getMock();

        mkdir('theme1');
        mkdir('theme2');
        touch('file1');
        touch('file2');

        $sut = new LocalThemeProvider($settingsRepository, $templateDir);
        $themes = $sut->getList();
        $this->assertInternalType('array', $themes);
        $this->assertEquals(2, count($themes));
        $this->assertEquals('theme1', $themes[0]);
        $this->assertEquals('theme2', $themes[1]);
    }

    public function testGetCurrent()
    {
        $templateDir = '.';
        $settingsRepository = $this->getMockBuilder('Looptribe\Paytoshi\Model\SettingsRepository')
            ->disableOriginalConstructor()
            ->getMock();
        $settingsRepository->method('get')
            ->willReturn('theme1');
        $sut = new LocalThemeProvider($settingsRepository, $templateDir);
        $currentTheme = $sut->getCurrent();
        $this->assertEquals('theme1', $currentTheme);
    }

    public function tearDown()
    {
        chdir('..');
        $this->rrmdir(self::TEST_DIRECTORY);
    }

    private function rrmdir($dir)
    {
        if (is_dir($dir)) {
            $objects = scandir($dir);
            foreach ($objects as $object) {
                if ($object != "." && $object != "..") {
                    if (is_dir($dir."/".$object)) {
                        $this->rrmdir($dir."/".$object);
                    } else {
                        unlink($dir."/".$object);
                    }
                }
            }
            rmdir($dir);
        }
    }
}
