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

    public function findLastByRecipientAndIp($ip, Recipient $recipient)
    {
        if (!$ip)
            return null;

        $address = $recipient->isNew() ? null : $recipient->getAddress();

        $qb = $this->queryBuilder->getLastByRecipientAndIp($ip, $address);
        $result = $qb->execute()->fetch();
        if(!$result)
            return null;

        return $this->payoutMapper->toModel($result);
    }
}