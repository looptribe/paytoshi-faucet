<?php

namespace Looptribe\Paytoshi\Tests\Integration;

use Looptribe\Paytoshi\Application;
use Silex\WebTestCase;

class SetupTest extends WebTestCase
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
            ->willReturn(null);
        $this->app['repository.settings'] = $mock;

        $client = $this->createClient();
        $client->request('GET', '/');

        $this->assertTrue($client->getResponse()->isRedirect());

        $crawler = $client->followRedirect();

        $this->assertGreaterThan(0, $crawler->filter('html:contains("Paytoshi Faucet setup")')->count());
    }
}
