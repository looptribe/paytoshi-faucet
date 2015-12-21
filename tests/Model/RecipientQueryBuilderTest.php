<?php

namespace Looptribe\Paytoshi\Tests\Model;


use Looptribe\Paytoshi\Model\Recipient;
use Looptribe\Paytoshi\Model\RecipientQueryBuilder;

class RecipientQueryBuilderTest extends \PHPUnit_Framework_TestCase
{
    public function testGetFindOneByAddressQuery()
    {
        $address = 'addr1';

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
            ->with('paytoshi_recipients')
            ->willReturn($qb);
        $qb->method('where')->willReturn($qb);
        $qb->method('setParameter')
            ->with($this->anything(), $address)
            ->willReturn($qb);
        $connection->method('createQueryBuilder')
            ->willReturn($qb);

        $sut = new RecipientQueryBuilder($connection);
        $result = $sut->getFindOneByAddressQuery($address);
        $this->assertInstanceOf('\Doctrine\DBAL\Query\QueryBuilder', $result);
    }

    public function testGetInsertQuery()
    {
        $self = $this;
        $recipient = new Recipient();
        $recipient->setAddress('addr1');
        $recipient->setEarning(100);
        $recipient->setReferralEarning(10);
        $recipient->setCreatedAt(new \DateTime());
        $recipient->setUpdatedAt(new \DateTime());

        $connection = $this->getMockBuilder('Doctrine\DBAL\Connection')
            ->disableOriginalConstructor()
            ->getMock();

        $qb = $this->getMockBuilder('\Doctrine\DBAL\Query\QueryBuilder')
            ->disableOriginalConstructor()
            ->getMock();
        $qb->method('insert')
            ->with('paytoshi_recipients')
            ->willReturn($qb);
        $qb->method('values')
            ->willReturn($qb);
        $qb->method('setParameter')
            ->willReturnCallback(function($field, $value) use ($self, $qb, $recipient) {
                switch($field)
                {
                    case 'address':
                        $self->assertEquals($value, $recipient->getAddress());
                        break;
                    case 'earning':
                        $self->assertEquals($value, $recipient->getEarning());
                        break;
                    case 'referral_earning':
                        $self->assertEquals($value, $recipient->getReferralEarning());
                        break;
                    case 'updated_at':
                        $self->assertEquals($value, $recipient->getUpdatedAt());
                        break;
                    case 'created_at':
                        $self->assertEquals($value, $recipient->getCreatedAt());
                        break;
                    default:
                        $self->fail('Invalid field');
                        break;
                }
                return $qb;
            });
        $connection->method('createQueryBuilder')
            ->willReturn($qb);

        $sut = new RecipientQueryBuilder($connection);
        $result = $sut->getInsertQuery($recipient);
        $this->assertInstanceOf('\Doctrine\DBAL\Query\QueryBuilder', $result);
    }

    public function testGetUpdateQuery()
    {
        $self = $this;
        $recipient = new Recipient();
        $recipient->setId(1);
        $recipient->setAddress('addr1');
        $recipient->setEarning(100);
        $recipient->setReferralEarning(10);
        $recipient->setCreatedAt(new \DateTime());
        $recipient->setUpdatedAt(new \DateTime());

        $connection = $this->getMockBuilder('Doctrine\DBAL\Connection')
            ->disableOriginalConstructor()
            ->getMock();

        $qb = $this->getMockBuilder('\Doctrine\DBAL\Query\QueryBuilder')
            ->disableOriginalConstructor()
            ->getMock();
        $qb->method('update')
            ->with('paytoshi_recipients')
            ->willReturn($qb);
        $qb->method('set')
            ->willReturn($qb);
        $qb->method('setParameter')
            ->willReturnCallback(function($field, $value) use ($self, $qb, $recipient) {
                switch($field)
                {
                    case 'address':
                        $self->assertEquals($value, $recipient->getAddress());
                        break;
                    case 'earning':
                        $self->assertEquals($value, $recipient->getEarning());
                        break;
                    case 'referral_earning':
                        $self->assertEquals($value, $recipient->getReferralEarning());
                        break;
                    case 'updated_at':
                        $self->assertEquals($value, $recipient->getUpdatedAt());
                        break;
                    default:
                        $self->fail('Invalid field');
                        break;
                }
                return $qb;
            });
        $connection->method('createQueryBuilder')
            ->willReturn($qb);

        $sut = new RecipientQueryBuilder($connection);
        $result = $sut->getUpdateQuery($recipient);
        $this->assertInstanceOf('\Doctrine\DBAL\Query\QueryBuilder', $result);
    }
}