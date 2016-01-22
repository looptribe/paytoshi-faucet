<?php

namespace Looptribe\Paytoshi\Tests\Integration;

use Looptribe\Paytoshi\Application;
use Looptribe\Paytoshi\Captcha\CaptchaProviderException;
use org\bovigo\vfs\vfsStream;
use Silex\WebTestCase;

class RewardTest extends WebTestCase
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
    public function testReward1()
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
        $crawler = $client->request('POST', '/reward');

        $this->assertTrue($client->getResponse()->isRedirect('/'));

        $crawler = $client->followRedirect();

        $this->assertTrue($client->getResponse()->isOk());
        $this->assertSame(1, $crawler->filter('.alert-warning')->count());
        $this->assertContains('Missing address', $crawler->filter('.alert-warning')->text());
    }

    /**
     * @runInSeparateProcess
     */
    public function testReward2()
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
        $crawler = $client->request('POST', '/reward', array('address' => 'addr1'));

        $this->assertTrue($client->getResponse()->isRedirect('/'));

        $crawler = $client->followRedirect();

        $this->assertTrue($client->getResponse()->isOk());
        $this->assertSame(1, $crawler->filter('.alert-warning')->count());
        $this->assertContains('Missing captcha', $crawler->filter('.alert-warning')->text());
    }

    /**
     * @runInSeparateProcess
     */
    public function testReward3()
    {
        $settingsRepository = $this->getMockBuilder('Looptribe\Paytoshi\Model\SettingsRepository')
            ->disableOriginalConstructor()
            ->getMock();
        $settingsRepository->expects($this->any())
            ->method('get')
            ->willReturnCallback(function ($params) {
                switch($params) {
                    case 'password':
                        return 'fakepasswordhash';
                    case 'theme':
                        return 'default';
                    case 'captcha_provider':
                        return 'funcaptcha';
                    case 'funcaptcha_public_key':
                        return 'pubkey';
                    case 'funcaptcha_private_key':
                        return 'privkey';
                    case 'referral_percentage':
                        return 30;
                }
            });
        $this->app['repository.settings'] = $settingsRepository;

        $captchaProvider = $this->getMock(
            'Looptribe\Paytoshi\Captcha\Funcaptcha\FuncaptchaProvider',
            array('checkAnswer'),
            array($this->app['buzz'], $this->app['repository.settings']->get('funcaptcha_public_key'), $this->app['repository.settings']->get('funcaptcha_private_key'))
        );
        $this->app['captcha.provider'] = $captchaProvider;

        $captchaProvider->method('checkAnswer')
            ->willThrowException(new CaptchaProviderException('Error'));

        $client = $this->createClient();
        $crawler = $client->request('POST', '/reward', array('address' => 'addr1', 'fc-token' => 'token'));

        $this->assertTrue($client->getResponse()->isRedirect('/'));

        $crawler = $client->followRedirect();

        $this->assertTrue($client->getResponse()->isOk());
        $this->assertSame(1, $crawler->filter('.alert-danger')->count());
        $this->assertContains('Error', $crawler->filter('.alert-danger')->text());
    }

    public function testRewardWithoutConfig()
    {
        vfsStream::setup('config');
        vfsStream::newFile('config/config.yml', 0000);
        $this->app['configPath'] = vfsStream::url('config/config.yml');

        $client = $this->createClient();
        $this->setExpectedException('Symfony\Component\Yaml\Exception\ParseException', 'Unable to parse "vfs://config/config.yml" as the file is not readable.');
        $crawler = $client->request('POST', '/reward');
    }
}
