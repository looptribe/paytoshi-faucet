<?php

namespace Looptribe\Paytoshi\Tests\Model;

use Looptribe\Paytoshi\Model\RecipientRepository;
use PHPUnit_Framework_MockObject_MockObject;

class RecipientRepositoryTest extends \PHPUnit_Framework_TestCase
{
    /** @var  PHPUnit_Framework_MockObject_MockObject */
    private $db;

    public function setUp()
    {
        $this->db = $this->getMockBuilder('Doctrine\DBAL\Connection')
            ->disableOriginalConstructor()
            ->getMock();
    }

    public function testFindOneByAddress1()
    {
        $qb = $this->getMockBuilder('\Doctrine\DBAL\Query\QueryBuilder')
            ->disableOriginalConstructor()
            ->getMock();

        $statement = $this->getMockBuilder('\Doctrine\DBAL\Driver\Statement')
            ->disableOriginalConstructor()
            ->getMock();

        $statement->method('fetch')
            ->willReturn(array(
                'id' => 1,
                'address' => 'addr1',
                'earning' => 100,
                'referral_earning' => 10,
                'created_at' => new \DateTime(),
                'updated_at' => new \DateTime(),
            ));

        $qb->method('select')->willReturn($qb);
        $qb->method('from')->willReturn($qb);
        $qb->method('where')->willReturn($qb);
        $qb->method('setParameter')
            ->with($this->anything(), 'addr1')
            ->willReturn($qb);
        $qb->method('execute')
            ->willReturn($statement);

        $this->db->method('createQueryBuilder')
            ->willReturn($qb);

        $sut = new RecipientRepository($this->db);
        $result = $sut->findOneByAddress('addr1');
        $this->assertInstanceOf('Looptribe\Paytoshi\Model\Recipient', $result);
        $this->assertEquals(1, $result->getId());
        $this->assertEquals('addr1', $result->getAddress());
        $this->assertEquals(100, $result->getEarning());
        $this->assertEquals(10, $result->getReferralEarning());
        $this->assertInstanceOf('DateTime', $result->getCreatedAt());
        $this->assertInstanceOf('DateTime', $result->getUpdatedAt());
    }

    public function testFindOneByAddress2()
    {
        $qb = $this->getMockBuilder('\Doctrine\DBAL\Query\QueryBuilder')
            ->disableOriginalConstructor()
            ->getMock();

        $statement = $this->getMockBuilder('\Doctrine\DBAL\Driver\Statement')
            ->disableOriginalConstructor()
            ->getMock();

        $statement->method('fetch')
            ->willReturn(null);

        $qb->method('select')->willReturn($qb);
        $qb->method('from')->willReturn($qb);
        $qb->method('where')->willReturn($qb);
        $qb->method('setParameter')
            ->with($this->anything(), 'addr1')
            ->willReturn($qb);
        $qb->method('execute')
            ->willReturn($statement);

        $this->db->method('createQueryBuilder')
            ->willReturn($qb);

        $sut = new RecipientRepository($this->db);
        $result = $sut->findOneByAddress('addr1');
        $this->assertNull($result);
    }
}