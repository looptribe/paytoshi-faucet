<?php

namespace Looptribe\Paytoshi\Tests\Logic;

use Looptribe\Paytoshi\Logic\IntervalEnforcer;
use Looptribe\Paytoshi\Model\Payout;
use Looptribe\Paytoshi\Model\Recipient;

class IntervalEnforcerTest extends \PHPUnit_Framework_TestCase
{
    /** @var \PHPUnit_Framework_MockObject_MockObject */
    private $payoutRepository;

    public function setUp()
    {
        $this->payoutRepository = $this->getMockBuilder('Looptribe\Paytoshi\Model\PayoutRepository')
            ->disableOriginalConstructor()
            ->getMock();
    }

    public function testCheck1()
    {
        $interval = 3600;

        $this->payoutRepository->method('findLastByRecipientAndIp')
            ->willReturn(false);

        $sut = new IntervalEnforcer($this->payoutRepository, $interval);
        $result = $sut->check('10.10.10.10', 'addr1');
        $this->assertNull($result);
    }

    public function testCheck2()
    {
        $interval = 3600;

        $payout = new Payout();
        $payout->setCreatedAt(new \DateTime('2016-01-01 10:00:00'));

        $this->payoutRepository->method('findLastByRecipientAndIp')
            ->willReturn($payout);

        $sut = new IntervalEnforcer($this->payoutRepository, $interval);
        $result = $sut->check('10.10.10.10', 'addr1');
        $this->assertNull($result);
    }

    public function testCheck3()
    {
        $interval = 3600;

        $tenMinuteAgo = new \DateTime();
        $tenMinuteAgo->sub(new \DateInterval('PT600S'));

        $payout = new Payout();
        $payout->setCreatedAt($tenMinuteAgo);

        $this->payoutRepository->method('findLastByRecipientAndIp')
            ->willReturn($payout);

        $sut = new IntervalEnforcer($this->payoutRepository, $interval);
        $result = $sut->check('10.10.10.10', 'addr1');
        $this->assertInstanceOf('DateInterval', $result);
        $this->assertSame(50, $result->i);
    }

    public function testCheck4()
    {
        $interval = -3600;

        $sut = new IntervalEnforcer($this->payoutRepository, $interval);
        $this->setExpectedException('Exception', 'Invalid waiting interval');
        $result = $sut->check('10.10.10.10', 'addr1');
    }

    public function testCheck5()
    {
        $interval = 'A';
        $this->setExpectedException('Exception', 'Invalid waiting interval format');

        $tenMinuteAgo = new \DateTime();
        $tenMinuteAgo->sub(new \DateInterval('PT600S'));

        $payout = new Payout();
        $payout->setCreatedAt($tenMinuteAgo);

        $this->payoutRepository->method('findLastByRecipientAndIp')
            ->willReturn($payout);

        $sut = new IntervalEnforcer($this->payoutRepository, $interval);
        $result = $sut->check('10.10.10.10', 'addr1');
    }

    public function testCheck6()
    {
        $interval = 3600;
        $this->setExpectedException('Exception', 'Invalid ip');

        $sut = new IntervalEnforcer($this->payoutRepository, $interval);
        $result = $sut->check('', 'addr1');
    }


    public function testCheck7()
    {
        $interval = 3600;
        $this->setExpectedException('Exception', 'Invalid address');

        $sut = new IntervalEnforcer($this->payoutRepository, $interval);
        $result = $sut->check('10.10.10.10', '');
    }

}