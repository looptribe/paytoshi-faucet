<?php

namespace Looptribe\Paytoshi\Model;

use Doctrine\DBAL\Connection;

class RecipientRepository
{
    /** @var Connection */
    private $database;

    const TABLE_NAME = 'paytoshi_recipients';

    public function __construct(Connection $database)
    {
        $this->database = $database;
    }

    /**
     * @param $address
     * @return Recipient|null
     */
    public function findOneByAddress($address)
    {
        $qb = $this->database->createQueryBuilder();
        $qb->select('*')
            ->from(self::TABLE_NAME)
            ->where('address', ':address')
            ->setParameter('address', $address);
        $result = $qb->execute()->fetch();
        if (!$result)
            return null;

        $recipient = new Recipient();
        $recipient->setId($result['id']);
        $recipient->setAddress($result['address']);
        $recipient->setEarning($result['earning']);
        $recipient->setReferralEarning($result['referral_earning']);
        $recipient->setCreatedAt($result['created_at']);
        $recipient->setUpdatedAt($result['updated_at']);
        return $recipient;
    }
}