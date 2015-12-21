<?php

namespace Looptribe\Paytoshi\Tests\Model;

use Looptribe\Paytoshi\Model\Recipient;
use Looptribe\Paytoshi\Model\RecipientRepository;
use PHPUnit_Framework_MockObject_MockObject;
use Symfony\Component\Security\Acl\Exception\Exception;

class RecipientRepositoryTest extends \PHPUnit_Framework_TestCase
{
    /** @var  PHPUnit_Framework_MockObject_MockObject */
    private $connection;

    /** @var  PHPUnit_Framework_MockObject_MockObject */
    private $mapper;

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


        $sut = new RecipientRepository($this->connection, $this->mapper, $this->queryBuilder);
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
        $recipient->setCreatedAt(new \DateTime());
        $recipient->setUpdatedAt(new \DateTime());

        $qb = $this->getMockBuilder('\Doctrine\DBAL\Query\QueryBuilder')
            ->disableOriginalConstructor()
            ->getMock();

        $this->queryBuilder->method('getInsertQuery')
            ->with($recipient)
            ->willReturn($qb);

        $statement = $this->getMockBuilder('\Doctrine\DBAL\Driver\Statement')
            ->disableOriginalConstructor()
            ->getMock();

        $statement->method('fetch')
            ->willReturn(true);

        $qb->method('execute')
            ->willReturn($statement);

        $this->connection->method('lastInsertId')
            ->willReturn(1);

        $sut = new RecipientRepository($this->connection, $this->mapper, $this->queryBuilder);
        $result = $sut->insert($recipient);
        $this->assertInstanceOf('Looptribe\Paytoshi\Model\Recipient', $result);
        $this->assertEquals(1, $result->getId());
        $this->assertEquals('addr1', $result->getAddress());
        $this->assertEquals(100, $result->getEarning());
        $this->assertEquals(10, $result->getReferralEarning());
        $this->assertInstanceOf('DateTime', $result->getCreatedAt());
        $this->assertInstanceOf('DateTime', $result->getUpdatedAt());
    }

    public function testInsert2()
    {
        $recipient = new Recipient();
        $recipient->setAddress('addr1');
        $recipient->setEarning(100);
        $recipient->setReferralEarning(10);
        $recipient->setCreatedAt(new \DateTime());
        $recipient->setUpdatedAt(new \DateTime());

        $qb = $this->getMockBuilder('\Doctrine\DBAL\Query\QueryBuilder')
            ->disableOriginalConstructor()
            ->getMock();

        $this->queryBuilder->method('getInsertQuery')
            ->with($recipient)
            ->willReturn($qb);

        $statement = $this->getMockBuilder('\Doctrine\DBAL\Driver\Statement')
            ->disableOriginalConstructor()
            ->getMock();

        $statement->method('fetch')
            ->willReturn(false);

        $qb->method('execute')
            ->willReturn($statement);

        $sut = new RecipientRepository($this->connection, $this->mapper, $this->queryBuilder);
        $result = $sut->insert($recipient);
        $this->assertNull($result);
    }

    public function testInsert3()
    {
        $recipient = new Recipient();
        $recipient->setEarning(100);
        $recipient->setReferralEarning(10);
        $recipient->setCreatedAt(new \DateTime());
        $recipient->setUpdatedAt(new \DateTime());

        $sut = new RecipientRepository($this->connection, $this->mapper, $this->queryBuilder);
        try {
            $result = $sut->insert($recipient);
        }
        catch (\Exception $e) {
            $this->assertInstanceOf('Exception', $e);
            $this->assertSame('Invalid Recipient', $e->getMessage());
        }
    }

}