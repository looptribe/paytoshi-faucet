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
        $payout->setCreatedAt(new \DateTime('2016-01-01 10:00:00'));

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
        $this->assertSame(50, $result->i);
    }

    public function testInterval4()
    {
        $payoutRepository = $this->getMockBuilder('Looptribe\Paytoshi\Model\PayoutRepository')
            ->disableOriginalConstructor()
            ->getMock();
        $interval = -3600;

        $sut = new IntervalEnforcer($payoutRepository, $interval);
        $this->setExpectedException('Exception', 'Invalid waiting interval');
        $result = $sut->check('10.10.10.10', new Recipient());
    }

    public function testInterval5()
    {
        $payoutRepository = $this->getMockBuilder('Looptribe\Paytoshi\Model\PayoutRepository')
            ->disableOriginalConstructor()
            ->getMock();
        $interval = 'A';
        $this->setExpectedException('Exception', 'Invalid waiting interval format');

        $tenMinuteAgo = new \DateTime();
        $tenMinuteAgo->sub(new \DateInterval('PT600S'));

        $payout = new Payout();
        $payout->setCreatedAt($tenMinuteAgo);

        $payoutRepository->method('findLastByRecipientAndIp')
            ->willReturn($payout);

        $sut = new IntervalEnforcer($payoutRepository, $interval);
        $result = $sut->check('10.10.10.10', new Recipient());
    }

}