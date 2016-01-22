<?php

namespace Looptribe\Paytoshi\Tests\Integration;

use Looptribe\Paytoshi\Application;
use org\bovigo\vfs\vfsStream;
use Silex\WebTestCase;

class IndexTest extends WebTestCase
{
    public function createApplication()
    {
        $app = new Application();
        $app['debug'] = true;
        unset($app['exception_handler']);
        return $app;
    }

    /**
     * @runInSeparateProcess
     */
    public function testIndex()
    {
        $mock = $this->getMockBuilder('Looptribe\Paytoshi\Model\SettingsRepository')
            ->disableOriginalConstructor()
            ->getMock();
        $mock->expects($this->any())
            ->method('get')
            ->willReturnCallback(function ($params) {
                switch($params) {
                    case 'password':
                        return 'fakepasswordhash';
                    case 'theme':
                        return 'default';
                    case 'captcha_provider':
                        return 'funcaptcha';
                }
            });
        $this->app['repository.settings'] = $mock;

        $client = $this->createClient();
        $crawler = $client->request('GET', '/');

        $this->assertTrue($client->getResponse()->isOk());
    }

    /**
     * @runInSeparateProcess
     */
    public function testIndexWithReferral()
    {
        $mock = $this->getMockBuilder('Looptribe\Paytoshi\Model\SettingsRepository')
            ->disableOriginalConstructor()
            ->getMock();
        $mock->expects($this->any())
            ->method('get')
            ->willReturnCallback(function ($params) {
                switch($params) {
                    case 'password':
                        return 'fakepasswordhash';
                    case 'theme':
                        return 'default';
                    case 'captcha_provider':
                        return 'funcaptcha';
                    case 'referral_percentage':
                        return 30;
                }
            });
        $this->app['repository.settings'] = $mock;

        $client = $this->createClient();
        $crawler = $client->request('GET', '/?r=addr1');

        $this->assertTrue($client->getResponse()->isOk());
        $this->assertContains('name="referral" value="addr1"', $client->getResponse()->getContent());
    }

    public function testIndexWithoutConfig()
    {
        vfsStream::setup('config');
        vfsStream::newFile('config/config.yml', 0700);
        $this->app['configPath'] = vfsStream::url('config/config.yml');

        $client = $this->createClient();
        $this->setExpectedException('Symfony\Component\Yaml\Exception\ParseException', 'Unable to parse "vfs://config/config.yml" as the file is not readable.');
        $crawler = $client->request('GET', '/');
    }
}
