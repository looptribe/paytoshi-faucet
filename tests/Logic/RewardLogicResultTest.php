<?php

namespace Looptribe\Paytoshi\Tests\Logic;

use Looptribe\Paytoshi\Api\Response\FaucetSendResponse;
use Looptribe\Paytoshi\Logic\RewardLogicResult;

class RewardLogicResultTest extends \PHPUnit_Framework_TestCase
{
    public function testConstructor1()
    {
        $sut = new RewardLogicResult();
        $sut->setSuccessful(true);
        $this->assertSame(true, $sut->isSuccessful());
        $this->assertSame(null, $sut->getSeverity());
        $this->assertSame(null, $sut->getError());
        $this->assertSame(null, $sut->getResponse());
    }

    public function testConstructor2()
    {
        $sut = new RewardLogicResult();
        $sut->setSeverity(RewardLogicResult::SEVERITY_DANGER);
        $this->assertSame(false, $sut->isSuccessful());
        $this->assertSame('danger', $sut->getSeverity());
        $this->assertSame(null, $sut->getError());
        $this->assertSame(null, $sut->getResponse());
    }

    public function testConstructor3()
    {
        $sut = new RewardLogicResult();
        $sut->setError('Message');
        $this->assertSame(false, $sut->isSuccessful());
        $this->assertSame(null, $sut->getSeverity());
        $this->assertSame('Message', $sut->getError());
        $this->assertSame(null, $sut->getResponse());
    }

    public function testConstructor4()
    {
        $response = $this->getMockBuilder('Looptribe\Paytoshi\Api\Response\FaucetSendResponse')
            ->disableOriginalConstructor()
            ->getMock();

        $sut = new RewardLogicResult();
        $sut->setResponse($response);
        $this->assertSame(false, $sut->isSuccessful());
        $this->assertSame(null, $sut->getSeverity());
        $this->assertSame(null, $sut->getError());
        $this->assertInstanceOf('Looptribe\Paytoshi\Api\Response\FaucetSendResponse', $sut->getResponse());
    }
}
