<?php

namespace Looptribe\Paytoshi\Tests\Logic;

use Looptribe\Paytoshi\Logic\IntervalEnforcer;
use Looptribe\Paytoshi\Model\Payout;
use Looptribe\Paytoshi\Model\Recipient;

class IntervalEnforcerTest extends \PHPUnit_Framework_TestCase
{
    public function testInterval1()
    {
        $payoutRepository = $this->getMockBuilder('Looptribe\Paytoshi\Model\PayoutRepository')
            ->disableOriginalConstructor()
            ->getMock();
        $interval = 3600;

        $payoutRepository->method('findLastByRecipientAndIp')
            ->willReturn(false);

        $sut = new IntervalEnforcer($payoutRepository, $interval);
        $result = $sut->check('10.10.10.10', new Recipient());
        $this->assertNull($result);
    }

    public function testInterval2()
    {
        $payoutRepository = $this->getMockBuilder('Looptribe\Paytoshi\Model\PayoutRepository')
            ->disableOriginalConstructor()
            ->getMock();
        $interval = 3600;

        $payout = new Payout();
        $payout->setCreatedAt(new \DateTime('first day of this year'));

        $payoutRepository->method('findLastByRecipientAndIp')
            ->willReturn($payout);

        $sut = new IntervalEnforcer($payoutRepository, $interval);
        $result = $sut->check('10.10.10.10', new Recipient());
        $this->assertNull($result);
    }

    public function testInterval3()
    {
        $payoutRepository = $this->getMockBuilder('Looptribe\Paytoshi\Model\PayoutRepository')
            ->disableOriginalConstructor()
            ->getMock();
        $interval = 3600;

        $tenMinuteAgo = new \DateTime();
        $tenMinuteAgo->sub(new \DateInterval('PT600S'));

        $payout = new Payout();
        $payout->setCreatedAt($tenMinuteAgo);

        $payoutRepository->method('findLastByRecipientAndIp')
            ->willReturn($payout);

        $sut = new IntervalEnforcer($payoutRepository, $interval);
        $result = $sut->check('10.10.10.10', new Recipient());
        $this->assertInstanceOf('DateInterval', $result);
    }

    public function testInterval4()
    {
        $payoutRepository = $this->getMockBuilder('Looptribe\Paytoshi\Model\PayoutRepository')
            ->disableOriginalConstructor()
            ->getMock();
        $interval = -3600;
        $this->setExpectedException('Exception', 'Invalid waiting interval');

        $sut = new IntervalEnforcer($payoutRepository, $interval);
        $result = $sut->check('10.10.10.10', new Recipient());
    }

}