<?php

namespace Looptribe\Paytoshi\Tests\Model;


use Looptribe\Paytoshi\Model\Recipient;
use Looptribe\Paytoshi\Model\RecipientQueryBuilder;

class RecipientQueryBuilderTest extends \PHPUnit_Framework_TestCase
{
    public function testGetFindOneByAddressQuery()
    {
        $address = 'addr1';

        $db = $this->getMockBuilder('Doctrine\DBAL\Connection')
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
        $db->method('createQueryBuilder')
            ->willReturn($qb);

        $sut = new RecipientQueryBuilder($db);
        $result = $sut->getFindOneByAddressQuery($address);
        $this->assertInstanceOf('\Doctrine\DBAL\Query\QueryBuilder', $result);
    }

    public function testGetInsertQuery()
    {
        $recipient = new Recipient();
        $recipient->setId(1);
        $recipient->setAddress('addr1');
        $recipient->setEarning(100);
        $recipient->setReferralEarning(10);
        $recipient->setCreatedAt(new \DateTime());
        $recipient->setUpdatedAt(new \DateTime());

        $db = $this->getMockBuilder('Doctrine\DBAL\Connection')
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
            ->will(
                $this->returnValueMap(array(
                        array('address', $recipient->getAddress()),
                        array('earning'), $recipient->getEarning(),
                        array('referral_earning', $recipient->getReferralEarning()),
                        array('created_at', $recipient->getCreatedAt()),
                        array('updated_at', $recipient->getUpdatedAt())
                ))
            )
            ->willReturn($qb);
        $db->method('createQueryBuilder')
            ->willReturn($qb);

        $sut = new RecipientQueryBuilder($db);
        $result = $sut->getInsertQuery($recipient);
        $this->assertInstanceOf('\Doctrine\DBAL\Query\QueryBuilder', $result);
    }
}