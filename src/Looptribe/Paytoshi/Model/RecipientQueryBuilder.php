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

    public function getInsertQuery(Recipient $recipient)
    {
        $qb = $this->database->createQueryBuilder();
        $qb->insert(self::TABLE_NAME)
            ->values(array(
                'address' => ':address',
                'earning' => ':earning',
                'referral_earning' => ':referral_earning',
                'created_at' => ':created_at',
                'updated_at' => ':updated_at'
            ))
            ->setParameter('address', $recipient->getAddress())
            ->setParameter('earning', $recipient->getEarning())
            ->setParameter('referral_earning', $recipient->getReferralEarning())
            ->setParameter('created_at', $recipient->getCreatedAt())
            ->setParameter('updated_at', $recipient->getUpdatedAt());

        return $qb;
    }

    public function getUpdateQuery(Recipient $recipient)
    {
        $qb = $this->database->createQueryBuilder();
        $qb->update(self::TABLE_NAME)
            ->set('earning', ':earning')
            ->set('referral_earning', ':referral_earning')
            ->set('updated_at', ':updated_at')
            ->setParameter('earning', $recipient->getEarning())
            ->setParameter('referral_earning', $recipient->getReferralEarning())
            ->setParameter('updated_at', $recipient->getUpdatedAt());

        return $qb;
    }
}