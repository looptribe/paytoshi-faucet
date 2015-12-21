<?php

namespace Looptribe\Paytoshi\Tests\Model;

use Looptribe\Paytoshi\Model\Payout;

class PayoutTest extends \PHPUnit_Framework_TestCase
{
    public function testConstructor()
    {
        $sut = new Payout();
        $this->assertEquals(0, $sut->getEarning());
        $this->assertEquals(0, $sut->getReferralEarning());
        $this->assertInstanceOf('DateTime', $sut->getCreatedAt());
    }

    public function testSetGetId()
    {
        $sut = new Payout();
        $result = $sut->setId(10);
        $this->assertInstanceOf('Looptribe\Paytoshi\Model\Payout', $result);
        $this->assertEquals(10, $sut->getId());
    }

    public function testSetGetIp()
    {
        $sut = new Payout();
        $result = $sut->setIp('10.10.10.10');
        $this->assertInstanceOf('Looptribe\Paytoshi\Model\Payout', $result);
        $this->assertEquals('10.10.10.10', $sut->getIp());
    }

    public function testSetGetRecipientAddress()
    {
        $sut = new Payout();
        $result = $sut->setRecipientAddress('addr');
        $this->assertInstanceOf('Looptribe\Paytoshi\Model\Payout', $result);
        $this->assertEquals('addr', $sut->getRecipientAddress());
    }

    public function testSetGetReferralRecipientAddress()
    {
        $sut = new Payout();
        $result = $sut->setReferralRecipientAddress('addr');
        $this->assertInstanceOf('Looptribe\Paytoshi\Model\Payout', $result);
        $this->assertEquals('addr', $sut->getReferralRecipientAddress());
    }

    public function testSetGetEarning()
    {
        $sut = new Payout();
        $result = $sut->setEarning(10);
        $this->assertInstanceOf('Looptribe\Paytoshi\Model\Payout', $result);
        $this->assertEquals(10, $sut->getEarning());
    }

    public function testSetGetReferralEarning()
    {
        $sut = new Payout();
        $result = $sut->setReferralEarning(10);
        $this->assertInstanceOf('Looptribe\Paytoshi\Model\Payout', $result);
        $this->assertEquals(10, $sut->getReferralEarning());
    }

    public function testSetGetCreatedAt()
    {
        $now = new \DateTime();
        $sut = new Payout();
        $result = $sut->setCreatedAt($now);
        $this->assertInstanceOf('Looptribe\Paytoshi\Model\Payout', $result);
        $this->assertEquals($now, $sut->getCreatedAt());
    }
}
