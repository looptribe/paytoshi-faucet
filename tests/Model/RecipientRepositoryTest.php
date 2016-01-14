<?php

namespace Looptribe\Paytoshi\Tests\Model;

use Looptribe\Paytoshi\Model\Recipient;
use Looptribe\Paytoshi\Model\RecipientRepository;
use PHPUnit_Framework_MockObject_MockObject;

class RecipientRepositoryTest extends \PHPUnit_Framework_TestCase
{
    /** @var  PHPUnit_Framework_MockObject_MockObject */
    private $connection;

    /** @var  PHPUnit_Framework_MockObject_MockObject */
    private $mapper;

    /** @var  PHPUnit_Framework_MockObject_MockObject */
    private $queryBuilder;

    public function setUp()
    {
        $this->connection = $this->getMockBuilder('Doctrine\DBAL\Connection')
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
            'created_at' => '2016-01-01 10:00:00',
            'updated_at' => '2016-01-01 10:00:00'
        );

        $recipient = new Recipient();
        $recipient->setId(1);
        $recipient->setAddress('addr1');
        $recipient->setEarning(100);
        $recipient->setReferralEarning(10);
        $recipient->setCreatedAt(new \DateTime('2016-01-01 10:00:00'));
        $recipient->setUpdatedAt(new \DateTime('2016-01-01 10:00:00'));

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


        $sut = new RecipientRepository($this->connection, $this->mapper, $this->queryBuilder);
        $result = $sut->findOneByAddress('addr1');
        $this->assertInstanceOf('Looptribe\Paytoshi\Model\Recipient', $result);
        $this->assertSame(1, $result->getId());
        $this->assertSame('addr1', $result->getAddress());
        $this->assertSame(100, $result->getEarning());
        $this->assertSame(10, $result->getReferralEarning());
        $this->assertInstanceOf('DateTime', $result->getCreatedAt());
        $this->assertSame('2016-01-01 10:00:00', $result->getCreatedAt()->format('Y-m-d H:i:s'));
        $this->assertInstanceOf('DateTime', $result->getUpdatedAt());
        $this->assertSame('2016-01-01 10:00:00', $result->getUpdatedAt()->format('Y-m-d H:i:s'));
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

        $this->connection->method('createQueryBuilder')
            ->willReturn($qb);

        $sut = new RecipientRepository($this->connection, $this->mapper, $this->queryBuilder);
        $result = $sut->findOneByAddress('addr1');
        $this->assertNull($result);
    }

    public function testFindOneByAddress3()
    {
        $sut = new RecipientRepository($this->connection, $this->mapper, $this->queryBuilder);
        $result = $sut->findOneByAddress(null);
        $this->assertNull($result);
    }

    public function testInsert1()
    {
        $recipient = new Recipient();
        $recipient->setAddress('addr1');
        $recipient->setEarning(100);
        $recipient->setReferralEarning(10);
        $recipient->setCreatedAt(new \DateTime('2016-01-01 10:00:00'));
        $recipient->setUpdatedAt(new \DateTime('2016-01-01 10:00:00'));

        $qb = $this->getMockBuilder('\Doctrine\DBAL\Query\QueryBuilder')
            ->disableOriginalConstructor()
            ->getMock();

        $this->queryBuilder->method('getInsertQuery')
            ->with($recipient)
            ->willReturn($qb);

        $statement = $this->getMockBuilder('\Doctrine\DBAL\Driver\Statement')
            ->disableOriginalConstructor()
            ->getMock();

        $qb->method('execute')
            ->willReturn(1);

        $this->connection->method('lastInsertId')
            ->willReturn(1);

        $sut = new RecipientRepository($this->connection, $this->mapper, $this->queryBuilder);
        $result = $sut->insert($recipient);
        $this->assertInstanceOf('Looptribe\Paytoshi\Model\Recipient', $result);
        $this->assertSame(1, $result->getId());
        $this->assertSame('addr1', $result->getAddress());
        $this->assertSame(100, $result->getEarning());
        $this->assertSame(10, $result->getReferralEarning());
        $this->assertInstanceOf('DateTime', $result->getCreatedAt());
        $this->assertSame('2016-01-01 10:00:00', $result->getCreatedAt()->format('Y-m-d H:i:s'));
        $this->assertInstanceOf('DateTime', $result->getUpdatedAt());
        $this->assertSame('2016-01-01 10:00:00', $result->getUpdatedAt()->format('Y-m-d H:i:s'));
    }

    public function testInsert2()
    {
        $recipient = new Recipient();
        $recipient->setAddress('addr1');
        $recipient->setEarning(100);
        $recipient->setReferralEarning(10);
        $recipient->setCreatedAt(new \DateTime('2016-01-01 10:00:00'));
        $recipient->setUpdatedAt(new \DateTime('2016-01-01 10:00:00'));

        $qb = $this->getMockBuilder('\Doctrine\DBAL\Query\QueryBuilder')
            ->disableOriginalConstructor()
            ->getMock();

        $this->queryBuilder->method('getInsertQuery')
            ->with($recipient)
            ->willReturn($qb);

        $statement = $this->getMockBuilder('\Doctrine\DBAL\Driver\Statement')
            ->disableOriginalConstructor()
            ->getMock();

        $qb->method('execute')
            ->willReturn(0);

        $sut = new RecipientRepository($this->connection, $this->mapper, $this->queryBuilder);
        $result = $sut->insert($recipient);
        $this->assertNull($result);
    }

    public function testInsert3()
    {
        $recipient = new Recipient();
        $recipient->setEarning(100);
        $recipient->setReferralEarning(10);
        $recipient->setCreatedAt(new \DateTime('2016-01-01 10:00:00'));
        $recipient->setUpdatedAt(new \DateTime('2016-01-01 10:00:00'));

        $sut = new RecipientRepository($this->connection, $this->mapper, $this->queryBuilder);
        $this->setExpectedException('\Exception', 'Invalid Recipient');
        $result = $sut->insert($recipient);
    }

}