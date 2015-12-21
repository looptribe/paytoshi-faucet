<?php

namespace Looptribe\Paytoshi\Tests\Model;

use Looptribe\Paytoshi\Model\Recipient;
use Looptribe\Paytoshi\Model\RecipientRepository;
use PHPUnit_Framework_MockObject_MockObject;

class RecipientRepositoryTest extends \PHPUnit_Framework_TestCase
{
    /** @var  PHPUnit_Framework_MockObject_MockObject */
    private $db;

    /** @var  PHPUnit_Framework_MockObject_MockObject */
    private $mapper;

    public function setUp()
    {
        $this->db = $this->getMockBuilder('Doctrine\DBAL\Connection')
            ->disableOriginalConstructor()
            ->getMock();

        $this->mapper = $this->getMock('Looptribe\Paytoshi\Model\RecipientMapper');

        $this->queryBuilder = $this->getMockBuilder('Looptribe\Paytoshi\Model\RecipientQueryBuilder')
            ->disableOriginalConstructor()
            ->getMock();
    }

    public function testFindOneByAddress1()
    {
        $data = array(
            'id' => 1,
            'address' => 'addr1',
            'earning' => 100,
            'referral_earning' => 10,
            'created_at' => new \DateTime(),
            'updated_at' => new \DateTime()
        );

        $recipient = new Recipient();
        $recipient->setId(1);
        $recipient->setAddress('addr1');
        $recipient->setEarning(100);
        $recipient->setReferralEarning(10);
        $recipient->setCreatedAt(new \DateTime());
        $recipient->setUpdatedAt(new \DateTime());

        $qb = $this->getMockBuilder('\Doctrine\DBAL\Query\QueryBuilder')
            ->disableOriginalConstructor()
            ->getMock();

        $this->queryBuilder->method('getFindOneByAddressQuery')
            ->with('addr1')
            ->willReturn($qb);

        $statement = $this->getMockBuilder('\Doctrine\DBAL\Driver\Statement')
            ->disableOriginalConstructor()
            ->getMock();

        $statement->method('fetch')
            ->willReturn($data);

        $this->mapper->method('toModel')
            ->willReturn($recipient);

        $qb->method('execute')
            ->willReturn($statement);


        $sut = new RecipientRepository($this->db, $this->mapper, $this->queryBuilder);
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

        $qb->method('execute')
            ->willReturn($statement);

        $this->queryBuilder->method('getFindOneByAddressQuery')
            ->with('addr1')
            ->willReturn($qb);

        $this->db->method('createQueryBuilder')
            ->willReturn($qb);

        $sut = new RecipientRepository($this->db, $this->mapper, $this->queryBuilder);
        $result = $sut->findOneByAddress('addr1');
        $this->assertNull($result);
    }
}