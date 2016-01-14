<?php

namespace Looptribe\Paytoshi\Tests\Model;

use Looptribe\Paytoshi\Model\Payout;
use Looptribe\Paytoshi\Model\PayoutQueryBuilder;

class PayoutQueryBuilderTest extends \PHPUnit_Framework_TestCase
{
    public function testGetLastByRecipientAndIp1()
    {
        $ip = '10.10.10.10';
        $address = 'addr1';

        $self = $this;
        $connection = $this->getMockBuilder('Doctrine\DBAL\Connection')
            ->disableOriginalConstructor()
            ->getMock();

        $qb = $this->getMockBuilder('\Doctrine\DBAL\Query\QueryBuilder')
            ->disableOriginalConstructor()
            ->getMock();
        $qb->method('select')
            ->with('*')
            ->willReturn($qb);
        $qb->method('from')
            ->with('paytoshi_payouts')
            ->willReturn($qb);
        $qb->method('where')
            ->willReturn($qb);
        $qb->expects($this->exactly(2))
            ->method('setParameter')
            ->willReturnCallback(function($field, $value) use ($self, $qb, $ip, $address) {
                switch ($field) {
                    case 'ip':
                        $self->assertSame($ip, $value);
                        break;
                    case 'address':
                        $self->assertSame($address, $value);
                        break;
                    default:
                        $self->fail('Invalid field');
                        break;
                }
                return $qb;
            });
        $qb->method('orderBy')
            ->with('created_at', 'DESC')
            ->willReturn($qb);
        $qb->method('orWhere')
            ->willReturn($qb);
        $connection->method('createQueryBuilder')
            ->willReturn($qb);

        $sut = new PayoutQueryBuilder($connection);
        $result = $sut->getLastByRecipientAndIpQuery($ip, $address);
        $this->assertInstanceOf('\Doctrine\DBAL\Query\QueryBuilder', $result);
    }

    public function testGetLastByRecipientAndIp2()
    {
        $ip = '10.10.10.10';

        $connection = $this->getMockBuilder('Doctrine\DBAL\Connection')
            ->disableOriginalConstructor()
            ->getMock();

        $qb = $this->getMockBuilder('\Doctrine\DBAL\Query\QueryBuilder')
            ->disableOriginalConstructor()
            ->getMock();
        $qb->method('select')
            ->with('*')
            ->willReturn($qb);
        $qb->method('from')
            ->with('paytoshi_payouts')
            ->willReturn($qb);
        $qb->method('where')
            ->willReturn($qb);
        $qb->expects($this->once())
            ->method('setParameter')
            ->with($this->anything(), $ip)
            ->willReturn($qb);
        $qb->method('orderBy')
            ->with('created_at', 'DESC')
            ->willReturn($qb);
        $connection->method('createQueryBuilder')
            ->willReturn($qb);

        $sut = new PayoutQueryBuilder($connection);
        $result = $sut->getLastByRecipientAndIpQuery($ip);
        $this->assertInstanceOf('\Doctrine\DBAL\Query\QueryBuilder', $result);
    }

    public function testGetInsertQuery1()
    {
        $self = $this;
        $payout = new Payout();
        $payout->setRecipientAddress('addr1');
        $payout->setEarning(100);
        $payout->setIp('10.10.10.10');
        $payout->setCreatedAt(new \DateTime());

        $connection = $this->getMockBuilder('Doctrine\DBAL\Connection')
            ->disableOriginalConstructor()
            ->getMock();

        $qb = $this->getMockBuilder('\Doctrine\DBAL\Query\QueryBuilder')
            ->disableOriginalConstructor()
            ->getMock();
        $qb->method('insert')
            ->with('paytoshi_payouts')
            ->willReturn($qb);
        $qb->method('values')
            ->willReturn($qb);
        $qb->method('setParameter')
            ->willReturnCallback(function($field, $value) use ($self, $qb, $payout) {
                switch($field)
                {
                    case 'recipient_address':
                        $self->assertSame($payout->getRecipientAddress(), $value);
                        break;
                    case 'earning':
                        $self->assertSame($payout->getEarning(), $value);
                        break;
                    case 'referral_recipient_address':
                        $self->assertSame(null, $value);
                        break;
                    case 'referral_earning':
                        $self->assertSame(0, $value);
                        break;
                    case 'ip':
                        $self->assertSame($payout->getIp(), $value);
                        break;
                    case 'created_at':
                        $self->assertSame($payout->getCreatedAt(), $value);
                        break;
                    default:
                        $self->fail('Invalid field');
                        break;
                }
                return $qb;
            });
        $connection->method('createQueryBuilder')
            ->willReturn($qb);

        $sut = new PayoutQueryBuilder($connection);
        $result = $sut->getInsertQuery($payout);
        $this->assertInstanceOf('\Doctrine\DBAL\Query\QueryBuilder', $result);
    }

    public function testGetInsertQuery2()
    {
        $self = $this;
        $payout = new Payout();
        $payout->setRecipientAddress('addr1');
        $payout->setEarning(100);
        $payout->setReferralRecipientAddress('refaddr1');
        $payout->setReferralEarning(10);
        $payout->setIp('10.10.10.10');
        $payout->setCreatedAt(new \DateTime());

        $connection = $this->getMockBuilder('Doctrine\DBAL\Connection')
            ->disableOriginalConstructor()
            ->getMock();

        $qb = $this->getMockBuilder('\Doctrine\DBAL\Query\QueryBuilder')
            ->disableOriginalConstructor()
            ->getMock();
        $qb->method('insert')
            ->with('paytoshi_payouts')
            ->willReturn($qb);
        $qb->method('values')
            ->willReturn($qb);
        $qb->method('setParameter')
            ->willReturnCallback(function($field, $value) use ($self, $qb, $payout) {
                switch($field)
                {
                    case 'recipient_address':
                        $self->assertSame($payout->getRecipientAddress(), $value);
                        break;
                    case 'earning':
                        $self->assertSame($payout->getEarning(), $value);
                        break;
                    case 'referral_recipient_address':
                        $self->assertSame($payout->getReferralRecipientAddress(), $value);
                        break;
                    case 'referral_earning':
                        $self->assertSame($payout->getReferralEarning(), $value);
                        break;
                    case 'ip':
                        $self->assertSame($payout->getIp(), $value);
                        break;
                    case 'created_at':
                        $self->assertSame($payout->getCreatedAt(), $value);
                        break;
                    default:
                        $self->fail('Invalid field');
                        break;
                }
                return $qb;
            });
        $connection->method('createQueryBuilder')
            ->willReturn($qb);

        $sut = new PayoutQueryBuilder($connection);
        $result = $sut->getInsertQuery($payout);
        $this->assertInstanceOf('\Doctrine\DBAL\Query\QueryBuilder', $result);
    }
}
