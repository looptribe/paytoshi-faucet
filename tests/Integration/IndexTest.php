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

    public function testIndex()
    {
        $this->markTestSkipped('Fails with \'headers already sent\'');

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
        $this->app['repository.settings'] = function () use ($mock) {
            return $mock;
        };

        $client = $this->createClient();
        $crawler = $client->request('GET', '/');

        $this->assertTrue($client->getResponse()->isOk());
    }
}
