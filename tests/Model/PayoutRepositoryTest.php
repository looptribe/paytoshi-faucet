<?php

namespace Looptribe\Paytoshi\Tests\Model;

use Looptribe\Paytoshi\Model\Payout;
use Looptribe\Paytoshi\Model\PayoutRepository;
use Looptribe\Paytoshi\Model\Recipient;
use PHPUnit_Framework_MockObject_MockObject;

class PayoutRepositoryTest extends \PHPUnit_Framework_TestCase
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

        $this->mapper = $this->getMock('Looptribe\Paytoshi\Model\PayoutMapper');

        $this->queryBuilder = $this->getMockBuilder('Looptribe\Paytoshi\Model\PayoutQueryBuilder')
            ->disableOriginalConstructor()
            ->getMock();
    }

    public function testFindLastByRecipientAndIp1()
    {
        $data = array(
            'id' => 1,
            'ip' => '10.10.10.10',
            'recipient_address' => 'addr1',
            'earning' => 100,
            'referral_earning' => 10,
            'recipient_referral_address' => 'addr2',
            'created_at' => '2016-01-01 10:00:00'
        );

        $ip = '10.10.10.10';

        $payout = new Payout();
        $payout->setId(1);
        $payout->setIp($ip);
        $payout->setRecipientAddress('addr1');
        $payout->setReferralRecipientAddress('addr2');
        $payout->setEarning(100);
        $payout->setReferralEarning(10);
        $payout->setCreatedAt(new \DateTime());

        $recipient = new Recipient();
        $recipient->setId(1);
        $recipient->setAddress('addr1');

        $qb = $this->getMockBuilder('\Doctrine\DBAL\Query\QueryBuilder')
            ->disableOriginalConstructor()
            ->getMock();

        $this->queryBuilder->method('getLastByRecipientAndIpQuery')
            ->with($ip, 'addr1')
            ->willReturn($qb);

        $statement = $this->getMockBuilder('\Doctrine\DBAL\Driver\Statement')
            ->disableOriginalConstructor()
            ->getMock();

        $statement->method('fetch')
            ->willReturn($data);

        $this->mapper->method('toModel')
            ->willReturn($payout);

        $qb->method('execute')
            ->willReturn($statement);


        $sut = new PayoutRepository($this->connection, $this->mapper, $this->queryBuilder);
        $result = $sut->findLastByRecipientAndIp($ip, $recipient);
        $this->assertInstanceOf('Looptribe\Paytoshi\Model\Payout', $result);
        $this->assertSame(1, $result->getId());
        $this->assertSame('10.10.10.10', $result->getIp());
        $this->assertSame('addr1', $result->getRecipientAddress());
        $this->assertSame('addr2', $result->getReferralRecipientAddress());
        $this->assertSame(100, $result->getEarning());
        $this->assertSame(10, $result->getReferralEarning());
        $this->assertInstanceOf('DateTime', $result->getCreatedAt());
    }

    public function testFindLastByRecipientAndIp2()
    {
        $data = array(
            'id' => 1,
            'ip' => '10.10.10.10',
            'recipient_address' => 'addr1',
            'earning' => 100,
            'referral_earning' => 10,
            'recipient_referral_address' => 'addr2',
            'created_at' => '2016-01-01 10:00:00'
        );

        $ip = '10.10.10.10';

        $payout = new Payout();
        $payout->setId(1);
        $payout->setIp($ip);
        $payout->setRecipientAddress('addr1');
        $payout->setReferralRecipientAddress('addr2');
        $payout->setEarning(100);
        $payout->setReferralEarning(10);
        $payout->setCreatedAt(new \DateTime());

        $recipient = new Recipient();
        $recipient->setAddress('addr1');

        $qb = $this->getMockBuilder('\Doctrine\DBAL\Query\QueryBuilder')
            ->disableOriginalConstructor()
            ->getMock();

        $this->queryBuilder->method('getLastByRecipientAndIpQuery')
            ->with($ip)
            ->willReturn($qb);

        $statement = $this->getMockBuilder('\Doctrine\DBAL\Driver\Statement')
            ->disableOriginalConstructor()
            ->getMock();

        $statement->method('fetch')
            ->willReturn($data);

        $this->mapper->method('toModel')
            ->willReturn($payout);

        $qb->method('execute')
            ->willReturn($statement);

        $sut = new PayoutRepository($this->connection, $this->mapper, $this->queryBuilder);
        $result = $sut->findLastByRecipientAndIp($ip, $recipient);
        $this->assertInstanceOf('Looptribe\Paytoshi\Model\Payout', $result);
        $this->assertSame(1, $result->getId());
        $this->assertSame('10.10.10.10', $result->getIp());
        $this->assertSame('addr1', $result->getRecipientAddress());
        $this->assertSame('addr2', $result->getReferralRecipientAddress());
        $this->assertSame(100, $result->getEarning());
        $this->assertSame(10, $result->getReferralEarning());
        $this->assertInstanceOf('DateTime', $result->getCreatedAt());
    }

    public function testFindLastByRecipientAndIp3()
    {
        $recipient = new Recipient();
        $recipient->setAddress('addr1');

        $sut = new PayoutRepository($this->connection, $this->mapper, $this->queryBuilder);
        $result = $sut->findLastByRecipientAndIp(null, $recipient);
        $this->assertNull($result);
    }

    public function testFindLastByRecipientAndIp4()
    {
        $ip = '10.10.10.10';

        $recipient = new Recipient();
        $recipient->setAddress('addr1');

        $qb = $this->getMockBuilder('\Doctrine\DBAL\Query\QueryBuilder')
            ->disableOriginalConstructor()
            ->getMock();

        $this->queryBuilder->method('getLastByRecipientAndIpQuery')
            ->with($ip)
            ->willReturn($qb);

        $statement = $this->getMockBuilder('\Doctrine\DBAL\Driver\Statement')
            ->disableOriginalConstructor()
            ->getMock();

        $statement->method('fetch')
            ->willReturn(false);

        $qb->method('execute')
            ->willReturn($statement);


        $sut = new PayoutRepository($this->connection, $this->mapper, $this->queryBuilder);
        $result = $sut->findLastByRecipientAndIp($ip, $recipient);
        $this->assertNull($result);
    }

    public function testFindLastByRecipientAndIp5()
    {
        $ip = '10.10.10.10';

        $recipient = new Recipient();
        $recipient->setAddress('addr1');
        $recipient->setId('1');

        $qb = $this->getMockBuilder('\Doctrine\DBAL\Query\QueryBuilder')
            ->disableOriginalConstructor()
            ->getMock();

        $this->queryBuilder->method('getLastByRecipientAndIpQuery')
            ->with($ip, $recipient->getAddress())
            ->willReturn($qb);

        $statement = $this->getMockBuilder('\Doctrine\DBAL\Driver\Statement')
            ->disableOriginalConstructor()
            ->getMock();

        $statement->method('fetch')
            ->willReturn(false);

        $qb->method('execute')
            ->willReturn($statement);


        $sut = new PayoutRepository($this->connection, $this->mapper, $this->queryBuilder);
        $result = $sut->findLastByRecipientAndIp($ip, $recipient);
        $this->assertNull($result);
    }

    public function testInsert1()
    {
        $payout = new Payout();
        $payout->setRecipientAddress('addr1');
        $payout->setEarning(100);
        $payout->setIp('10.10.10.10');
        $payout->setCreatedAt(new \DateTime('2016-01-01 10:00:00'));

        $qb = $this->getMockBuilder('\Doctrine\DBAL\Query\QueryBuilder')
            ->disableOriginalConstructor()
            ->getMock();

        $this->queryBuilder->method('getInsertQuery')
            ->with($payout)
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

        $sut = new PayoutRepository($this->connection, $this->mapper, $this->queryBuilder);
        $result = $sut->insert($payout);
        $this->assertInstanceOf('Looptribe\Paytoshi\Model\Payout', $result);
        $this->assertSame(1, $result->getId());
        $this->assertSame('addr1', $result->getRecipientAddress());
        $this->assertSame(100, $result->getEarning());
        $this->assertSame(null, $result->getReferralRecipientAddress());
        $this->assertSame(0, $result->getReferralEarning());
        $this->assertSame('10.10.10.10', $result->getIp());
        $this->assertInstanceOf('DateTime', $result->getCreatedAt());
        $this->assertSame('2016-01-01 10:00:00', $result->getCreatedAt()->format('Y-m-d H:i:s'));
    }

    public function testInsert2()
    {
        $payout = new Payout();
        $payout->setRecipientAddress('addr1');
        $payout->setEarning(100);
        $payout->setReferralRecipientAddress('refaddr1');
        $payout->setReferralEarning(10);
        $payout->setIp('10.10.10.10');
        $payout->setCreatedAt(new \DateTime('2016-01-01 10:00:00'));

        $qb = $this->getMockBuilder('\Doctrine\DBAL\Query\QueryBuilder')
            ->disableOriginalConstructor()
            ->getMock();

        $this->queryBuilder->method('getInsertQuery')
            ->with($payout)
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

        $sut = new PayoutRepository($this->connection, $this->mapper, $this->queryBuilder);
        $result = $sut->insert($payout);
        $this->assertInstanceOf('Looptribe\Paytoshi\Model\Payout', $result);
        $this->assertSame(1, $result->getId());
        $this->assertSame('addr1', $result->getRecipientAddress());
        $this->assertSame(100, $result->getEarning());
        $this->assertSame('refaddr1', $result->getReferralRecipientAddress());
        $this->assertSame(10, $result->getReferralEarning());
        $this->assertSame('10.10.10.10', $result->getIp());
        $this->assertInstanceOf('DateTime', $result->getCreatedAt());
        $this->assertSame('2016-01-01 10:00:00', $result->getCreatedAt()->format('Y-m-d H:i:s'));
    }

    public function testInsert3()
    {
        $payout = new Payout();
        $payout->setRecipientAddress('addr1');
        $payout->setEarning(100);
        $payout->setIp('10.10.10.10');
        $payout->setCreatedAt(new \DateTime('2016-01-01 10:00:00'));

        $qb = $this->getMockBuilder('\Doctrine\DBAL\Query\QueryBuilder')
            ->disableOriginalConstructor()
            ->getMock();

        $this->queryBuilder->method('getInsertQuery')
            ->with($payout)
            ->willReturn($qb);

        $statement = $this->getMockBuilder('\Doctrine\DBAL\Driver\Statement')
            ->disableOriginalConstructor()
            ->getMock();

        $statement->method('fetch')
            ->willReturn(false);

        $qb->method('execute')
            ->willReturn($statement);

        $sut = new PayoutRepository($this->connection, $this->mapper, $this->queryBuilder);
        $result = $sut->insert($payout);
        $this->assertNull($result);
    }
}