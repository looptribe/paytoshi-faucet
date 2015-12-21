<?php

namespace Looptribe\Paytoshi\Tests\Model;

use Looptribe\Paytoshi\Model\Payout;
use Looptribe\Paytoshi\Model\PayoutMapper;

class PayoutMapperTest extends \PHPUnit_Framework_TestCase
{
    public function testToModel1()
    {
        $data = array(
            'id' => 1,
            'ip' => '10.10.10.10',
            'recipient_address' => 'addr1',
            'earning' => 100,
            'created_at' => new \DateTime(),
            'referral_recipient_address' => 'addr2',
            'referral_earning' => 10

        );
        $sut = new PayoutMapper();
        $model = $sut->toModel($data);
        $this->assertSame(1, $model->getId());
        $this->assertSame('10.10.10.10', $model->getIp());
        $this->assertSame('addr1', $model->getRecipientAddress());
        $this->assertSame(100, $model->getEarning());
        $this->assertSame(10, $model->getReferralEarning());
        $this->assertSame('addr2', $model->getReferralRecipientAddress());
        $this->assertInstanceOf('DateTime', $model->getCreatedAt());
    }

    public function testToModel2()
    {
        $data = array(
            'ip' => '10.10.10.10',
            'recipient_address' => 'addr1',
            'earning' => 100,
            'created_at' => new \DateTime(),
            'referral_recipient_address' => 'addr2',
            'referral_earning' => 10
        );
        $sut = new PayoutMapper();
        $model = $sut->toModel($data);
        $this->assertNull($model->getId());
        $this->assertSame('10.10.10.10', $model->getIp());
        $this->assertSame('addr1', $model->getRecipientAddress());
        $this->assertSame(100, $model->getEarning());
        $this->assertSame(10, $model->getReferralEarning());
        $this->assertSame('addr2', $model->getReferralRecipientAddress());
        $this->assertInstanceOf('DateTime', $model->getCreatedAt());
    }

    public function testToModel3()
    {
        $data = array(
            'id' => 1,
            'ip' => '10.10.10.10',
            'earning' => 100,
            'created_at' => new \DateTime(),
            'referral_recipient_address' => 'addr2',
            'referral_earning' => 10
        );
        $sut = new PayoutMapper();
        $model = $sut->toModel($data);
        $this->assertSame(1, $model->getId());
        $this->assertSame('10.10.10.10', $model->getIp());
        $this->assertNull($model->getRecipientAddress());
        $this->assertSame(100, $model->getEarning());
        $this->assertSame(10, $model->getReferralEarning());
        $this->assertSame('addr2', $model->getReferralRecipientAddress());
        $this->assertInstanceOf('DateTime', $model->getCreatedAt());
    }

    public function testToModel4()
    {
        $data = array(
            'id' => 1,
            'ip' => '10.10.10.10',
            'recipient_address' => 'addr1',
            'created_at' => new \DateTime(),
            'referral_recipient_address' => 'addr2',
            'referral_earning' => 10
        );
        $sut = new PayoutMapper();
        $model = $sut->toModel($data);
        $this->assertSame(1, $model->getId());
        $this->assertSame('10.10.10.10', $model->getIp());
        $this->assertSame('addr1', $model->getRecipientAddress());
        $this->assertSame(0, $model->getEarning());
        $this->assertSame(10, $model->getReferralEarning());
        $this->assertSame('addr2', $model->getReferralRecipientAddress());
        $this->assertInstanceOf('DateTime', $model->getCreatedAt());
    }

    public function testToModel5()
    {
        $data = array(
            'id' => 1,
            'ip' => '10.10.10.10',
            'recipient_address' => 'addr1',
            'earning' => 100,
            'created_at' => new \DateTime(),
            'referral_recipient_address' => 'addr2'
        );
        $sut = new PayoutMapper();
        $model = $sut->toModel($data);
        $this->assertSame(1, $model->getId());
        $this->assertSame('10.10.10.10', $model->getIp());
        $this->assertSame('addr1', $model->getRecipientAddress());
        $this->assertSame(100, $model->getEarning());
        $this->assertSame(0, $model->getReferralEarning());
        $this->assertSame('addr2', $model->getReferralRecipientAddress());
        $this->assertInstanceOf('DateTime', $model->getCreatedAt());
    }

    public function testToModel6()
    {
        $data = array(
            'id' => 1,
            'ip' => '10.10.10.10',
            'recipient_address' => 'addr1',
            'earning' => 100,
            'created_at' => new \DateTime(),
            'referral_earning' => 10
        );
        $sut = new PayoutMapper();
        $model = $sut->toModel($data);
        $this->assertSame(1, $model->getId());
        $this->assertSame('10.10.10.10', $model->getIp());
        $this->assertSame('addr1', $model->getRecipientAddress());
        $this->assertSame(100, $model->getEarning());
        $this->assertSame(10, $model->getReferralEarning());
        $this->assertNull($model->getReferralRecipientAddress());
        $this->assertInstanceOf('DateTime', $model->getCreatedAt());
    }

    public function testToModel7()
    {
        $data = array(
            'id' => 1,
            'ip' => '10.10.10.10',
            'recipient_address' => 'addr1',
            'earning' => 100,
            'referral_recipient_address' => 'addr2',
            'referral_earning' => 10
        );
        $sut = new PayoutMapper();
        $model = $sut->toModel($data);
        $this->assertSame(1, $model->getId());
        $this->assertSame('10.10.10.10', $model->getIp());
        $this->assertSame('addr1', $model->getRecipientAddress());
        $this->assertSame(100, $model->getEarning());
        $this->assertSame(10, $model->getReferralEarning());
        $this->assertSame('addr2', $model->getReferralRecipientAddress());
        $this->assertInstanceOf('DateTime', $model->getCreatedAt());
    }

    public function testToModel8()
    {
        $data = array(
            'id' => 1,
            'recipient_address' => 'addr1',
            'earning' => 100,
            'created_at' => new \DateTime(),
            'referral_recipient_address' => 'addr2',
            'referral_earning' => 10

        );
        $sut = new PayoutMapper();
        $model = $sut->toModel($data);
        $this->assertSame(1, $model->getId());
        $this->assertSame('addr1', $model->getRecipientAddress());
        $this->assertSame(100, $model->getEarning());
        $this->assertSame(10, $model->getReferralEarning());
        $this->assertSame('addr2', $model->getReferralRecipientAddress());
        $this->assertNull($model->getIp());
        $this->assertInstanceOf('DateTime', $model->getCreatedAt());
    }

    public function testToArray1()
    {
        $payout = new Payout();
        $payout->setId(1);
        $payout->setIp('10.10.10.10');
        $payout->setRecipientAddress('addr1');
        $payout->setEarning(100);
        $payout->setReferralEarning(10);
        $payout->setReferralRecipientAddress('addr2');
        $payout->setCreatedAt(new \DateTime());

        $sut = new PayoutMapper();
        $data = $sut->toArray($payout);
        $this->assertSame(1, $data['id']);
        $this->assertSame('10.10.10.10', $data['ip']);
        $this->assertSame('addr1', $data['recipient_address']);
        $this->assertSame(100, $data['earning']);
        $this->assertSame(10, $data['referral_earning']);
        $this->assertSame('addr2', $data['referral_recipient_address']);
        $this->assertInstanceOf('DateTime', $data['created_at']);
    }

    public function testToArray2()
    {
        $payout = new Payout();
        $payout->setIp('10.10.10.10');
        $payout->setRecipientAddress('addr1');
        $payout->setEarning(100);
        $payout->setReferralEarning(10);
        $payout->setReferralRecipientAddress('addr2');
        $payout->setCreatedAt(new \DateTime());

        $sut = new PayoutMapper();
        $data = $sut->toArray($payout);
        $this->assertNull($data['id']);
        $this->assertSame('10.10.10.10', $data['ip']);
        $this->assertSame('addr1', $data['recipient_address']);
        $this->assertSame(100, $data['earning']);
        $this->assertSame(10, $data['referral_earning']);
        $this->assertSame('addr2', $data['referral_recipient_address']);
        $this->assertInstanceOf('DateTime', $data['created_at']);
    }

    public function testToArray3()
    {
        $payout = new Payout();
        $payout->setId(1);
        $payout->setIp('10.10.10.10');
        $payout->setEarning(100);
        $payout->setReferralEarning(10);
        $payout->setReferralRecipientAddress('addr2');
        $payout->setCreatedAt(new \DateTime());

        $sut = new PayoutMapper();
        $data = $sut->toArray($payout);
        $this->assertSame(1, $data['id']);
        $this->assertSame('10.10.10.10', $data['ip']);
        $this->assertNull($data['recipient_address']);
        $this->assertSame(100, $data['earning']);
        $this->assertSame(10, $data['referral_earning']);
        $this->assertSame('addr2', $data['referral_recipient_address']);
        $this->assertInstanceOf('DateTime', $data['created_at']);
    }

    public function testToArray4()
    {
        $payout = new Payout();
        $payout->setId(1);
        $payout->setIp('10.10.10.10');
        $payout->setRecipientAddress('addr1');
        $payout->setReferralEarning(10);
        $payout->setReferralRecipientAddress('addr2');
        $payout->setCreatedAt(new \DateTime());

        $sut = new PayoutMapper();
        $data = $sut->toArray($payout);
        $this->assertSame(1, $data['id']);
        $this->assertSame('10.10.10.10', $data['ip']);
        $this->assertSame('addr1', $data['recipient_address']);
        $this->assertSame(0, $data['earning']);
        $this->assertSame(10, $data['referral_earning']);
        $this->assertSame('addr2', $data['referral_recipient_address']);
        $this->assertInstanceOf('DateTime', $data['created_at']);
    }

    public function testToArray5()
    {
        $payout = new Payout();
        $payout->setId(1);
        $payout->setIp('10.10.10.10');
        $payout->setRecipientAddress('addr1');
        $payout->setEarning(100);
        $payout->setReferralRecipientAddress('addr2');
        $payout->setCreatedAt(new \DateTime());

        $sut = new PayoutMapper();
        $data = $sut->toArray($payout);
        $this->assertSame(1, $data['id']);
        $this->assertSame('10.10.10.10', $data['ip']);
        $this->assertSame('addr1', $data['recipient_address']);
        $this->assertSame(100, $data['earning']);
        $this->assertSame(0, $data['referral_earning']);
        $this->assertSame('addr2', $data['referral_recipient_address']);
        $this->assertInstanceOf('DateTime', $data['created_at']);
    }

    public function testToArray6()
    {
        $payout = new Payout();
        $payout->setId(1);
        $payout->setIp('10.10.10.10');
        $payout->setRecipientAddress('addr1');
        $payout->setEarning(100);
        $payout->setReferralEarning(10);
        $payout->setCreatedAt(new \DateTime());

        $sut = new PayoutMapper();
        $data = $sut->toArray($payout);
        $this->assertSame(1, $data['id']);
        $this->assertSame('10.10.10.10', $data['ip']);
        $this->assertSame('addr1', $data['recipient_address']);
        $this->assertSame(100, $data['earning']);
        $this->assertSame(10, $data['referral_earning']);
        $this->assertNull($data['referral_recipient_address']);
        $this->assertInstanceOf('DateTime', $data['created_at']);
    }

    public function testToArray7()
    {
        $payout = new Payout();
        $payout->setId(1);
        $payout->setIp('10.10.10.10');
        $payout->setRecipientAddress('addr1');
        $payout->setEarning(100);
        $payout->setReferralEarning(10);
        $payout->setReferralRecipientAddress('addr2');

        $sut = new PayoutMapper();
        $data = $sut->toArray($payout);
        $this->assertSame(1, $data['id']);
        $this->assertSame('10.10.10.10', $data['ip']);
        $this->assertSame('addr1', $data['recipient_address']);
        $this->assertSame(100, $data['earning']);
        $this->assertSame(10, $data['referral_earning']);
        $this->assertSame('addr2', $data['referral_recipient_address']);
        $this->assertInstanceOf('DateTime', $data['created_at']);
    }

    public function testToArray8()
    {
        $payout = new Payout();
        $payout->setId(1);
        $payout->setRecipientAddress('addr1');
        $payout->setEarning(100);
        $payout->setReferralEarning(10);
        $payout->setReferralRecipientAddress('addr2');
        $payout->setCreatedAt(new \DateTime());

        $sut = new PayoutMapper();
        $data = $sut->toArray($payout);
        $this->assertSame(1, $data['id']);
        $this->assertNull($data['ip']);
        $this->assertSame('addr1', $data['recipient_address']);
        $this->assertSame(100, $data['earning']);
        $this->assertSame(10, $data['referral_earning']);
        $this->assertSame('addr2', $data['referral_recipient_address']);
        $this->assertInstanceOf('DateTime', $data['created_at']);
    }
}