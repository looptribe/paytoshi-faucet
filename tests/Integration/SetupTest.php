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
        $setupDiagnostics = $this->getMockBuilder('Looptribe\Paytoshi\Model\SetupDiagnostics')
            ->disableOriginalConstructor()
            ->getMock();
        $setupDiagnostics->expects($this->any())
            ->method('requiresSetup')
            ->willReturn(true);
        $this->app['setup.diagnostics'] = $setupDiagnostics;

        $client = $this->createClient();
        $client->request('GET', '/');

        $this->assertTrue($client->getResponse()->isRedirect());

        $crawler = $client->followRedirect();

        $this->assertGreaterThan(0, $crawler->filter('html:contains("Paytoshi Faucet setup")')->count());
    }

    public function testStartDeniedAlreadyCompleted()
    {
        $settingsRepository = $this->getMockBuilder('Looptribe\Paytoshi\Model\SettingsRepository')
            ->disableOriginalConstructor()
            ->getMock();
        $settingsRepository->expects($this->any())
            ->method('get')
            ->with('password')
            ->willReturn('fakepwd');
        $this->app['repository.settings'] = $settingsRepository;

        $setupDiagnostics = $this->getMockBuilder('Looptribe\Paytoshi\Model\SetupDiagnostics')
            ->disableOriginalConstructor()
            ->getMock();
        $setupDiagnostics->expects($this->any())
            ->method('requiresSetup')
            ->willReturn(false);
        $this->app['setup.diagnostics'] = $setupDiagnostics;

        $this->setExpectedException('Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException');

        $client = $this->createClient();
        $client->request('GET', '/setup/');
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
        $setupDiagnostics->expects($this->any())
            ->method('requiresSetup')
            ->willReturn(true);
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
        $setupDiagnostics->expects($this->any())
            ->method('requiresSetup')
            ->willReturn(true);
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

    public function testComplete()
    {
        $setupDiagnostics = $this->getMockBuilder('Looptribe\Paytoshi\Model\SetupDiagnostics')
            ->disableOriginalConstructor()
            ->getMock();
        $setupDiagnostics->expects($this->any())
            ->method('requiresSetup')
            ->willReturn(true);
        $this->app['setup.diagnostics'] = $setupDiagnostics;

        $configurator = $this->getMockBuilder('Looptribe\Paytoshi\Model\Configurator')
            ->disableOriginalConstructor()
            ->getMock();
        $configurator->expects($this->any())
            ->method('setup')
            ->willReturn(array(
                'password' => 'autogeneratedpassword123',
            ));
        $this->app['setup.configurator'] = $configurator;

        $client = $this->createClient();
        $crawler = $client->request('GET', '/setup/complete');

        $this->assertTrue($client->getResponse()->isSuccessful());

        $this->assertSame(1, $crawler->filter('html:contains("autogeneratedpassword123")')->count());
    }
}
