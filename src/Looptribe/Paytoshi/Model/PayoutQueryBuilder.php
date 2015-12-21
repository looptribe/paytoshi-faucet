<?php

namespace Looptribe\Paytoshi\Model;


use Doctrine\DBAL\Connection;

class PayoutQueryBuilder
{
    const TABLE_NAME = 'paytoshi_payouts';

    /** @var Connection */
    private $connection;

    public function __construct(Connection $connection)
    {
        $this->connection = $connection;
    }

    public function getLastByRecipientAndIp($ip, $address = null)
    {
        $qb = $this->connection->createQueryBuilder();
        $qb->select('*')
            ->from(self::TABLE_NAME)
            ->where('ip', ':ip')
            ->setParameter('ip', $ip)
            ->orderBy('created_at', 'DESC')
            ->setMaxResults(1);

        if ($address)
            $qb->orWhere('recipient_address', ':address')
                ->setParameter('address', $address);

        return $qb;
    }
}