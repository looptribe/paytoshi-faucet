<?php

namespace Looptribe\Paytoshi\Model;

use Doctrine\DBAL\Connection;

class RecipientRepository
{
    /** @var Connection */
    private $connection;

    /** @var RecipientMapper */
    private $recipientMapper;

    /** @var RecipientQueryBuilder */
    private $queryBuilder;

    public function __construct(Connection $connection, RecipientMapper $recipientMapper, RecipientQueryBuilder $queryBuilder)
    {
        $this->connection = $connection;
        $this->recipientMapper = $recipientMapper;
        $this->queryBuilder = $queryBuilder;
    }

    /**
     * @param $address
     * @return Recipient|null
     */
    public function findOneByAddress($address)
    {
        if (!$address)
            return null;

        $qb = $this->queryBuilder->getFindOneByAddressQuery($address);
        $result = $qb->execute()->fetch();
        if (!$result)
            return null;

        return $this->recipientMapper->toModel($result);
    }

    /**
     * @param Recipient $recipient
     * @return Recipient|null
     * @throws \Exception
     */
    public function insert(Recipient $recipient)
    {
        if (!$recipient || !$recipient->getAddress())
            throw new \Exception('Invalid Recipient');

        $qb = $this->queryBuilder->getInsertQuery($recipient);
        $result = $qb->execute();
        if (!$result)
            return null;

        $recipient->setId($this->connection->lastInsertId());
        return $recipient;
    }
}