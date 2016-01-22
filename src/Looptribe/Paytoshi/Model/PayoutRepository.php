<?php

namespace Looptribe\Paytoshi\Model;

use Doctrine\DBAL\Connection;

class PayoutRepository
{
    /** @var PayoutQueryBuilder */
    private $queryBuilder;

    /** @var Connection */
    private $connection;

    /** @var PayoutMapper */
    private $payoutMapper;

    public function __construct(Connection $connection, PayoutMapper $payoutMapper, PayoutQueryBuilder $queryBuilder)
    {
        $this->connection = $connection;
        $this->payoutMapper = $payoutMapper;
        $this->queryBuilder = $queryBuilder;
    }

    /**
     * @param $ip
     * @param string|null $address
     * @return Payout|null
     */
    public function findLastByRecipientAndIp($ip, $address = null)
    {
        if (!$ip)
            return null;

        $qb = $this->queryBuilder->getLastByRecipientAndIpQuery($ip, $address);
        $result = $qb->execute()->fetch();
        if(!$result)
            return null;

        return $this->payoutMapper->toModel($result);
    }

    /**
     * @param Payout $payout
     * @return Payout|null
     */
    public function insert(Payout $payout)
    {
        $qb = $this->queryBuilder->getInsertQuery($payout);
        $result = $qb->execute();
        if (!$result)
            return null;

        $payout->setId($this->connection->lastInsertId());
        return $payout;
    }
}