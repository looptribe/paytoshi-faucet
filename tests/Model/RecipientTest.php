<?php

namespace Looptribe\Paytoshi\Tests\Model;


use Looptribe\Paytoshi\Model\Recipient;

class RecipientTest extends \PHPUnit_Framework_TestCase
{
    public function testConstructor()
    {
        $sut = new Recipient();
        $this->assertEquals(0, $sut->getEarning());
        $this->assertEquals(0, $sut->getReferralEarning());
        $this->assertInstanceOf('DateTime', $sut->getCreatedAt());
        $this->assertInstanceOf('DateTime', $sut->getUpdatedAt());
    }

    public function testSetGetId()
    {
        $sut = new Recipient();
        $result = $sut->setId(10);
        $this->assertInstanceOf('Looptribe\Paytoshi\Model\Recipient', $result);
        $this->assertEquals(10, $sut->getId());
    }

    public function testSetGetAddress()
    {
        $sut = new Recipient();
        $result = $sut->setAddress('addr');
        $this->assertInstanceOf('Looptribe\Paytoshi\Model\Recipient', $result);
        $this->assertEquals('addr', $sut->getAddress());
    }

    public function testSetGetEarning()
    {
        $sut = new Recipient();
        $result = $sut->setEarning(10);
        $this->assertInstanceOf('Looptribe\Paytoshi\Model\Recipient', $result);
        $this->assertEquals(10, $sut->getEarning());
    }

    public function testSetGetReferralEarning()
    {
        $sut = new Recipient();
        $result = $sut->setReferralEarning(10);
        $this->assertInstanceOf('Looptribe\Paytoshi\Model\Recipient', $result);
        $this->assertEquals(10, $sut->getReferralEarning());
    }

    public function testSetGetCreatedAt()
    {
        $now = new \DateTime();
        $sut = new Recipient();
        $result = $sut->setCreatedAt($now);
        $this->assertInstanceOf('Looptribe\Paytoshi\Model\Recipient', $result);
        $this->assertEquals($now, $sut->getCreatedAt());
    }

    public function testSetGetUpdatedAt()
    {
        $now = new \DateTime();
        $sut = new Recipient();
        $result = $sut->setUpdatedAt($now);
        $this->assertInstanceOf('Looptribe\Paytoshi\Model\Recipient', $result);
        $this->assertEquals($now, $sut->getUpdatedAt());
    }

    public function testIsNew()
    {
        $sut = new Recipient();
        $this->assertTrue($sut->isNew());
        $sut->setId(10);
        $this->assertFalse($sut->isNew());
    }
}
