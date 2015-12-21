<?php

namespace Looptribe\Paytoshi\Tests\Model;

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
        $qb->method('where')->willReturn($qb);
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
            });
        $connection->method('createQueryBuilder')
            ->willReturn($qb);

        $sut = new PayoutQueryBuilder($connection);
        $result = $sut->getLastByRecipientAndIp($ip, $address);
        $this->assertInstanceOf('\Doctrine\DBAL\Query\QueryBuilder', $result);
    }

    public function testGetLastByRecipientAndIp2()
    {
        $ip = '10.10.10.10';

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
        $qb->method('where')->willReturn($qb);
        $qb->expects($this->once())
            ->method('setParameter')
            ->with($this->anything(), $ip);
        $connection->method('createQueryBuilder')
            ->willReturn($qb);

        $sut = new PayoutQueryBuilder($connection);
        $result = $sut->getLastByRecipientAndIp($ip);
        $this->assertInstanceOf('\Doctrine\DBAL\Query\QueryBuilder', $result);
    }
}
