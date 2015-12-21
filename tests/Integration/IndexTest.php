<?php

namespace Looptribe\Paytoshi\Tests\Integration;

use Looptribe\Paytoshi\Application;
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
                }
            });
        $this->app['repository.settings'] = $mock;

        $client = $this->createClient();
        $crawler = $client->request('GET', '/');

        $this->assertTrue($client->getResponse()->isOk());
    }
}
