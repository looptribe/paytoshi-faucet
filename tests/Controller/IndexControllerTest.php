<?php

namespace Looptribe\Paytoshi\Tests\Controller;


use Looptribe\Paytoshi\Controller\IndexController;

class IndexControllerTest extends \PHPUnit_Framework_TestCase
{
    public function testIndex()
    {
        $templating = $this->getMockBuilder('Looptribe\Paytoshi\Templating\TemplatingEngineInterface')
            ->disableOriginalConstructor()
            ->getMock();

        $templating->expects($this->once())
            ->method('render')
            ->with('default/layout.html.twig');

        $sut = new IndexController($templating);
        $sut->action();
    }
}
