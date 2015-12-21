<?php

namespace Looptribe\Paytoshi\Model;


use Doctrine\DBAL\Connection;

class RecipientQueryBuilder
{
    const TABLE_NAME = 'paytoshi_recipients';


    /** @var Connection */
    private $database;

    public function __construct(Connection $database)
    {
        $this->database = $database;
    }

    public function getFindOneByAddressQuery($address)
    {
        $qb = $this->database->createQueryBuilder();
        $qb->select('*')
            ->from(self::TABLE_NAME)
            ->where('address', ':address')
            ->setParameter('address', $address);
        return $qb;
    }
}