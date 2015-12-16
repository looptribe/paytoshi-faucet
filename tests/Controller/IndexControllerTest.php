<?php

namespace Looptribe\Paytoshi\Tests\Controller;


use Looptribe\Paytoshi\Controller\IndexController;
use Symfony\Component\HttpFoundation\Response;

class IndexControllerTest extends \PHPUnit_Framework_TestCase
{
    public function testAction()
    {
        $templating = $this->getMock('Looptribe\Paytoshi\Templating\TemplatingEngineInterface');

        $templating->expects($this->once())
            ->method('render')
            ->with('default/layout.html.twig')
            ->willReturn(new Response());

        $sut = new IndexController($templating);
        $response = $sut->action();

        $this->assertInstanceOf('Symfony\Component\HttpFoundation\Response', $response);
    }
}
