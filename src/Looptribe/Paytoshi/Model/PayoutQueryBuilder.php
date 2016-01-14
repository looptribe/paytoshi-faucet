<?php

namespace Looptribe\Paytoshi\Model;

use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Types\Type;

class PayoutQueryBuilder
{
    const TABLE_NAME = 'paytoshi_payouts';

    /** @var Connection */
    private $connection;

    public function __construct(Connection $connection)
    {
        $this->connection = $connection;
    }

    public function getLastByRecipientAndIpQuery($ip, $address = null)
    {
        $qb = $this->connection->createQueryBuilder();
        $qb->select('*')
            ->from(self::TABLE_NAME)
            ->where('ip', ':ip')
            ->setParameter('ip', $ip)
            ->orderBy('created_at', 'DESC')
            ->setMaxResults(1);

        if ($address) {
            $qb->orWhere('recipient_address', ':address')
                ->setParameter('address', $address);
        }

        return $qb;
    }

    /**
     * @param Payout $payout
     * @return \Doctrine\DBAL\Query\QueryBuilder
     */
    public function getInsertQuery(Payout $payout)
    {
        $qb = $this->connection->createQueryBuilder();
        $qb->insert(self::TABLE_NAME)
            ->values(array(
                'recipient_address' => ':recipient_address',
                'earning' => ':earning',
                'referral_recipient_address' => ':referral_recipient_address',
                'referral_earning' => ':referral_earning',
                'ip' => ':ip',
                'created_at' => ':created_at'
            ))
            ->setParameter('recipient_address', $payout->getRecipientAddress())
            ->setParameter('earning', $payout->getEarning())
            ->setParameter('referral_recipient_address', $payout->getReferralRecipientAddress())
            ->setParameter('referral_earning', $payout->getReferralEarning())
            ->setParameter('ip', $payout->getIp())
            ->setParameter('created_at', $payout->getCreatedAt(), Type::DATETIME);

        return $qb;
    }
}