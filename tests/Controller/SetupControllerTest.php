<?php

namespace Looptribe\Paytoshi\Tests\Controller;


use Looptribe\Paytoshi\Controller\SetupController;
use Symfony\Component\HttpFoundation\Response;

class SetupControllerTest extends \PHPUnit_Framework_TestCase
{
    /** @var SetupController */
    private $sut;

    /** @var \PHPUnit_Framework_MockObject_MockObject */
    private $templating;

    /** @var \PHPUnit_Framework_MockObject_MockObject */
    private $diagnostics;

    /** @var \PHPUnit_Framework_MockObject_MockObject */
    private $configurator;

    public function setUp()
    {
        $this->templating = $this->getMock('Looptribe\Paytoshi\Templating\TemplatingEngineInterface');
        $this->diagnostics = $this->getMockBuilder('Looptribe\Paytoshi\Model\SetupDiagnostics')
            ->disableOriginalConstructor()
            ->getMock();
        $this->configurator = $this->getMockBuilder('Looptribe\Paytoshi\Model\Configurator')
            ->disableOriginalConstructor()
            ->getMock();

        $this->sut = new SetupController($this->templating, $this->diagnostics, $this->configurator);
    }

    public function testAction()
    {
        $this->templating->method('render')
            ->with(
                'admin/setup_completed.html.twig',
                $this->arrayHasKey('results')
            )
            ->willReturn(new Response());

        $response = $this->sut->action();
        $this->assertInstanceOf('Symfony\Component\HttpFoundation\Response', $response);
    }

    public function testStartAction1()
    {
        $this->templating->method('render')
            ->with(
                'admin/setup.html.twig',
                $this->logicalAnd(
                    $this->arrayHasKey('dbException'),
                    $this->arrayHasKey('errors')
                )
            )
            ->willReturn(new Response());

        $response = $this->sut->startAction();
        $this->assertInstanceOf('Symfony\Component\HttpFoundation\Response', $response);
    }

    public function testStartAction2()
    {
        $this->diagnostics->method('checkDatabase')
            ->willThrowException(new \Exception());

        $this->templating->method('render')
            ->with(
                'admin/setup.html.twig',
                $this->logicalAnd(
                    $this->arrayHasKey('dbException'),
                    $this->arrayHasKey('errors')
                )
            )
            ->willReturn(new Response());

        $response = $this->sut->startAction();
        $this->assertInstanceOf('Symfony\Component\HttpFoundation\Response', $response);
    }
}
