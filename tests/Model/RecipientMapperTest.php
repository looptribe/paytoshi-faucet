<?php

namespace Looptribe\Paytoshi\Tests\Model;

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
        $this->assertEquals(1, $model->getId());
        $this->assertEquals('addr1', $model->getAddress());
        $this->assertEquals(100, $model->getEarning());
        $this->assertEquals(10, $model->getReferralEarning());
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
        $this->assertEquals('addr1', $model->getAddress());
        $this->assertEquals(100, $model->getEarning());
        $this->assertEquals(10, $model->getReferralEarning());
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
        $this->assertEquals(1, $model->getId());
        $this->assertNull($model->getAddress());
        $this->assertEquals(100, $model->getEarning());
        $this->assertEquals(10, $model->getReferralEarning());
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
        $this->assertEquals(1, $model->getId());
        $this->assertEquals('addr1', $model->getAddress());
        $this->assertEquals(0, $model->getEarning());
        $this->assertEquals(10, $model->getReferralEarning());
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
        $this->assertEquals(1, $model->getId());
        $this->assertEquals('addr1', $model->getAddress());
        $this->assertEquals(100, $model->getEarning());
        $this->assertEquals(0, $model->getReferralEarning());
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
        $this->assertEquals(1, $model->getId());
        $this->assertEquals('addr1', $model->getAddress());
        $this->assertEquals(100, $model->getEarning());
        $this->assertEquals(10, $model->getReferralEarning());
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
        $this->assertEquals(1, $model->getId());
        $this->assertEquals('addr1', $model->getAddress());
        $this->assertEquals(100, $model->getEarning());
        $this->assertEquals(10, $model->getReferralEarning());
        $this->assertInstanceOf('DateTime', $model->getCreatedAt());
        $this->assertInstanceOf('DateTime', $model->getUpdatedAt());
    }
}