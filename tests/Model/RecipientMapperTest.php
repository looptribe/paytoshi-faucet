<?php

namespace Looptribe\Paytoshi\Tests\Model;

use Looptribe\Paytoshi\Model\Recipient;
use Looptribe\Paytoshi\Model\RecipientMapper;

class RecipientMapperTest extends \PHPUnit_Framework_TestCase
{
    public function testToModel1()
    {
        $data = array(
            'id' => 1,
            'address' => 'addr1',
            'earning' => 100,
            'referral_earning' => 10,
            'created_at' => new \DateTime(),
            'updated_at' => new \DateTime()
        );
        $sut = new RecipientMapper();
        $model = $sut->toModel($data);
        $this->assertSame(1, $model->getId());
        $this->assertSame('addr1', $model->getAddress());
        $this->assertSame(100, $model->getEarning());
        $this->assertSame(10, $model->getReferralEarning());
        $this->assertInstanceOf('DateTime', $model->getCreatedAt());
        $this->assertInstanceOf('DateTime', $model->getUpdatedAt());
    }

    public function testToModel2()
    {
        $data = array(
            'address' => 'addr1',
            'earning' => 100,
            'referral_earning' => 10,
            'created_at' => new \DateTime(),
            'updated_at' => new \DateTime()
        );
        $sut = new RecipientMapper();
        $model = $sut->toModel($data);
        $this->assertNull($model->getId());
        $this->assertSame('addr1', $model->getAddress());
        $this->assertSame(100, $model->getEarning());
        $this->assertSame(10, $model->getReferralEarning());
        $this->assertInstanceOf('DateTime', $model->getCreatedAt());
        $this->assertInstanceOf('DateTime', $model->getUpdatedAt());
    }

    public function testToModel3()
    {
        $data = array(
            'id' => 1,
            'earning' => 100,
            'referral_earning' => 10,
            'created_at' => new \DateTime(),
            'updated_at' => new \DateTime()
        );
        $sut = new RecipientMapper();
        $model = $sut->toModel($data);
        $this->assertSame(1, $model->getId());
        $this->assertNull($model->getAddress());
        $this->assertSame(100, $model->getEarning());
        $this->assertSame(10, $model->getReferralEarning());
        $this->assertInstanceOf('DateTime', $model->getCreatedAt());
        $this->assertInstanceOf('DateTime', $model->getUpdatedAt());
    }

    public function testToModel4()
    {
        $data = array(
            'id' => 1,
            'address' => 'addr1',
            'referral_earning' => 10,
            'created_at' => new \DateTime(),
            'updated_at' => new \DateTime()
        );
        $sut = new RecipientMapper();
        $model = $sut->toModel($data);
        $this->assertSame(1, $model->getId());
        $this->assertSame('addr1', $model->getAddress());
        $this->assertSame(0, $model->getEarning());
        $this->assertSame(10, $model->getReferralEarning());
        $this->assertInstanceOf('DateTime', $model->getCreatedAt());
        $this->assertInstanceOf('DateTime', $model->getUpdatedAt());
    }

    public function testToModel5()
    {
        $data = array(
            'id' => 1,
            'address' => 'addr1',
            'earning' => 100,
            'created_at' => new \DateTime(),
            'updated_at' => new \DateTime()
        );
        $sut = new RecipientMapper();
        $model = $sut->toModel($data);
        $this->assertSame(1, $model->getId());
        $this->assertSame('addr1', $model->getAddress());
        $this->assertSame(100, $model->getEarning());
        $this->assertSame(0, $model->getReferralEarning());
        $this->assertInstanceOf('DateTime', $model->getCreatedAt());
        $this->assertInstanceOf('DateTime', $model->getUpdatedAt());
    }

    public function testToModel6()
    {
        $data = array(
            'id' => 1,
            'address' => 'addr1',
            'earning' => 100,
            'referral_earning' => 10,
            'updated_at' => new \DateTime()
        );
        $sut = new RecipientMapper();
        $model = $sut->toModel($data);
        $this->assertSame(1, $model->getId());
        $this->assertSame('addr1', $model->getAddress());
        $this->assertSame(100, $model->getEarning());
        $this->assertSame(10, $model->getReferralEarning());
        $this->assertInstanceOf('DateTime', $model->getCreatedAt());
        $this->assertInstanceOf('DateTime', $model->getUpdatedAt());
    }

    public function testToModel7()
    {
        $data = array(
            'id' => 1,
            'address' => 'addr1',
            'earning' => 100,
            'referral_earning' => 10,
            'created_at' => new \DateTime(),
        );
        $sut = new RecipientMapper();
        $model = $sut->toModel($data);
        $this->assertSame(1, $model->getId());
        $this->assertSame('addr1', $model->getAddress());
        $this->assertSame(100, $model->getEarning());
        $this->assertSame(10, $model->getReferralEarning());
        $this->assertInstanceOf('DateTime', $model->getCreatedAt());
        $this->assertInstanceOf('DateTime', $model->getUpdatedAt());
    }

    public function testToArray1()
    {
        $recipient = new Recipient();
        $recipient->setId(1);
        $recipient->setAddress('addr1');
        $recipient->setEarning(100);
        $recipient->setReferralEarning(10);
        $recipient->setCreatedAt(new \DateTime());
        $recipient->setUpdatedAt(new \DateTime());

        $sut = new RecipientMapper();
        $data = $sut->toArray($recipient);
        $this->assertSame(1, $data['id']);
        $this->assertSame('addr1', $data['address']);
        $this->assertSame(100, $data['earning']);
        $this->assertSame(10, $data['referral_earning']);
        $this->assertInstanceOf('DateTime', $data['created_at']);
        $this->assertInstanceOf('DateTime', $data['updated_at']);
    }

    public function testToArray2()
    {
        $recipient = new Recipient();
        $recipient->setId(1);
        $recipient->setEarning(100);
        $recipient->setReferralEarning(10);
        $recipient->setCreatedAt(new \DateTime());
        $recipient->setUpdatedAt(new \DateTime());

        $sut = new RecipientMapper();
        $data = $sut->toArray($recipient);
        $this->assertSame(1, $data['id']);
        $this->assertNull($data['address']);
        $this->assertSame(100, $data['earning']);
        $this->assertSame(10, $data['referral_earning']);
        $this->assertInstanceOf('DateTime', $data['created_at']);
        $this->assertInstanceOf('DateTime', $data['updated_at']);
    }

    public function testToArray3()
    {
        $recipient = new Recipient();
        $recipient->setId(1);
        $recipient->setAddress('addr1');
        $recipient->setReferralEarning(10);
        $recipient->setCreatedAt(new \DateTime());
        $recipient->setUpdatedAt(new \DateTime());

        $sut = new RecipientMapper();
        $data = $sut->toArray($recipient);
        $this->assertSame(1, $data['id']);
        $this->assertSame('addr1', $data['address']);
        $this->assertSame(0, $data['earning']);
        $this->assertSame(10, $data['referral_earning']);
        $this->assertInstanceOf('DateTime', $data['created_at']);
        $this->assertInstanceOf('DateTime', $data['updated_at']);
    }

    public function testToArray4()
    {
        $recipient = new Recipient();
        $recipient->setId(1);
        $recipient->setAddress('addr1');
        $recipient->setEarning(100);
        $recipient->setCreatedAt(new \DateTime());
        $recipient->setUpdatedAt(new \DateTime());

        $sut = new RecipientMapper();
        $data = $sut->toArray($recipient);
        $this->assertSame(1, $data['id']);
        $this->assertSame('addr1', $data['address']);
        $this->assertSame(100, $data['earning']);
        $this->assertSame(0, $data['referral_earning']);
        $this->assertInstanceOf('DateTime', $data['created_at']);
        $this->assertInstanceOf('DateTime', $data['updated_at']);
    }

    public function testToArray5()
    {
        $recipient = new Recipient();
        $recipient->setId(1);
        $recipient->setAddress('addr1');
        $recipient->setEarning(100);
        $recipient->setReferralEarning(10);
        $recipient->setUpdatedAt(new \DateTime());

        $sut = new RecipientMapper();
        $data = $sut->toArray($recipient);
        $this->assertSame(1, $data['id']);
        $this->assertSame('addr1', $data['address']);
        $this->assertSame(100, $data['earning']);
        $this->assertSame(10, $data['referral_earning']);
        $this->assertInstanceOf('DateTime', $data['created_at']);
        $this->assertInstanceOf('DateTime', $data['updated_at']);
    }

    public function testToArray6()
    {
        $recipient = new Recipient();
        $recipient->setId(1);
        $recipient->setAddress('addr1');
        $recipient->setEarning(100);
        $recipient->setReferralEarning(10);
        $recipient->setCreatedAt(new \DateTime());

        $sut = new RecipientMapper();
        $data = $sut->toArray($recipient);
        $this->assertSame(1, $data['id']);
        $this->assertSame('addr1', $data['address']);
        $this->assertSame(100, $data['earning']);
        $this->assertSame(10, $data['referral_earning']);
        $this->assertInstanceOf('DateTime', $data['created_at']);
        $this->assertInstanceOf('DateTime', $data['updated_at']);
    }

    public function testToArray7()
    {
        $recipient = new Recipient();
        $recipient->setAddress('addr1');
        $recipient->setEarning(100);
        $recipient->setReferralEarning(10);
        $recipient->setCreatedAt(new \DateTime());
        $recipient->setUpdatedAt(new \DateTime());

        $sut = new RecipientMapper();
        $data = $sut->toArray($recipient);
        $this->assertNull($data['id']);
        $this->assertSame('addr1', $data['address']);
        $this->assertSame(100, $data['earning']);
        $this->assertSame(10, $data['referral_earning']);
        $this->assertInstanceOf('DateTime', $data['created_at']);
        $this->assertInstanceOf('DateTime', $data['updated_at']);
    }
}