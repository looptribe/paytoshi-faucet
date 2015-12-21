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
    public function testStart()
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

    /**
     * @runInSeparateProcess
     */
    public function testCheck()
    {
        $setupDiagnostics = $this->getMockBuilder('Looptribe\Paytoshi\Model\SetupDiagnostics')
            ->disableOriginalConstructor()
            ->getMock();
        $setupDiagnostics->expects($this->once())
            ->method('checkDatabase');
        $this->app['setup.diagnostics'] = $setupDiagnostics;

        $client = $this->createClient();
        $content = '{"database":{"dbname":"paytoshi_faucet","user":"username","password":"pass","host":"localhost"}}';
        $client->request('POST', '/setup/check.json', array(), array(), array('CONTENT_TYPE' => 'application/json'), $content);

        $this->assertTrue($client->getResponse()->isSuccessful());
        $this->assertTrue($client->getResponse()->headers->contains('Content-Type', 'application/json'), 'Reponse should be application/json');

        $response = json_decode($client->getResponse()->getContent(), true);

        $this->assertTrue($response['ok'], 'Response should contains an ok field with a true value');
        $this->assertFalse($response['errors']['db']);
    }

    /**
     * @runInSeparateProcess
     */
    public function testCheckDbFail()
    {
        $setupDiagnostics = $this->getMockBuilder('Looptribe\Paytoshi\Model\SetupDiagnostics')
            ->disableOriginalConstructor()
            ->getMock();
        $setupDiagnostics->expects($this->once())
            ->method('checkDatabase')
            ->willThrowException(new \RuntimeException('Failed!'));
        $this->app['setup.diagnostics'] = $setupDiagnostics;

        $client = $this->createClient();
        $content = '{"database":{"dbname":"paytoshi_faucet","user":"username","password":"pass","host":"localhost"}}';
        $client->request('POST', '/setup/check.json', array(), array(), array('CONTENT_TYPE' => 'application/json'), $content);

        $this->assertTrue($client->getResponse()->isSuccessful());
        $this->assertTrue($client->getResponse()->headers->contains('Content-Type', 'application/json'), 'Reponse should be application/json');

        $response = json_decode($client->getResponse()->getContent(), true);

        $this->assertFalse($response['ok'], 'Response should contains an ok field with a false value');
        $this->assertNotEmpty($response['errors']['db']);
    }
}
