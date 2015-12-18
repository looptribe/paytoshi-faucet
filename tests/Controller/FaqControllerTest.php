<?php

namespace Looptribe\Paytoshi\Tests\Controller;


use Looptribe\Paytoshi\Controller\FaqController;

class FaqControllerTest extends \PHPUnit_Framework_TestCase
{
    public function testAction()
    {
        $themeProvider = $this->getMock('Looptribe\Paytoshi\Templating\ThemeProviderInterface');
        $templating = $this->getMock('Looptribe\Paytoshi\Templating\TemplatingEngineInterface');
        $settingsRepository = $this->getMockBuilder('Looptribe\Paytoshi\Model\SettingsRepository')
            ->disableOriginalConstructor()
            ->getMock();

        $themeProvider->method('getTemplate')
            ->willReturn('default/faq.html.twig');

        $templating->method('render')
            ->with(
                'default/faq.html.twig',
                $this->anything()
            )
            ->willReturn($this->getMock('Symfony\Component\HttpFoundation\Response'));

        $sut = new FaqController($templating, $themeProvider, $settingsRepository);
        $response = $sut->action();

        $this->assertInstanceOf('Symfony\Component\HttpFoundation\Response', $response);
    }
}