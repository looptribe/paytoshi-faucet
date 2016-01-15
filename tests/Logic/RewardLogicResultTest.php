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
        $this->assertSame(null, $sut->getMessage());
        $this->assertSame(null, $sut->getResponse());
    }

    public function testConstructor2()
    {
        $sut = new RewardLogicResult();
        $sut->setSeverity(RewardLogicResult::SEVERITY_SUCCESS);
        $this->assertSame(false, $sut->isSuccessful());
        $this->assertSame('success', $sut->getSeverity());
        $this->assertSame(null, $sut->getMessage());
        $this->assertSame(null, $sut->getResponse());
    }

    public function testConstructor3()
    {
        $sut = new RewardLogicResult();
        $sut->setMessage('Message');
        $this->assertSame(false, $sut->isSuccessful());
        $this->assertSame(null, $sut->getSeverity());
        $this->assertSame('Message', $sut->getMessage());
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
        $this->assertSame(null, $sut->getMessage());
        $this->assertInstanceOf('Looptribe\Paytoshi\Api\Response\FaucetSendResponse', $sut->getResponse());
    }
}
