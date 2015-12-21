<?php

namespace Looptribe\Paytoshi\Tests\Model;


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
}