<?php

/**
 * Paytoshi Faucet Script
 * 
 * Contact: info@paytoshi.org
 * 
 * @author: Looptribe
 * @link: https://paytoshi.org
 * @package: Looptribe\Paytoshi 
 */

namespace Looptribe\Paytoshi\Model;

use Looptribe\Paytoshi\Service\DatabaseService;

class RecipientRepository {
    
    /* @var $database DatabaseService */
    private $database;
    const TABLE_NAME = 'paytoshi_recipients';
    
    public function __construct($database)
    {
        $this->database = $database;
    }
    
    public function findOneByAddress($address) {
        $sql = sprintf("SELECT id, address, earning, referral_earning, "
                . "created_at, updated_at FROM %s WHERE address = :address", self::TABLE_NAME);
        $results = $this->database->run($sql, array(':address' => $address));
        if (empty($results))
            return null;
        
        $data = $results[0];
        
        $recipient = new Recipient();
        $recipient->setId($data['id']);
        $recipient->setAddress($data['address']);
        $recipient->setEarning($data['earning']);
        $recipient->setReferralEarning($data['referral_earning']);
        $recipient->setCreatedAt($data['created_at']);
        $recipient->setUpdatedAt($data['updated_at']);
        return $recipient;
    }
    
    public function save(Recipient $recipient) {
        $now = new \DateTime();
        if ($recipient->getId()) {
            $sql = sprintf("UPDATE %s SET earning = :earning, referral_earning = :referral_earning, "
                    . "updated_at = :updated_at WHERE address = :address", self::TABLE_NAME);
            $params = array(
                ':address' => $recipient->getAddress(),
                ':earning' => $recipient->getEarning(),
                ':referral_earning' => $recipient->getReferralEarning(),
                ':updated_at' => $now->format('Y-m-d H:i:s')
            );
        }
        else {
            $sql = sprintf("INSERT INTO %s (address, earning, referral_earning, created_at, updated_at)  "
                . "VALUES (:address, :earning, :referral_earning, :created_at, :updated_at)", self::TABLE_NAME);
            $params = array(
                ':address' => $recipient->getAddress(),
                ':earning' => $recipient->getEarning(),
                ':referral_earning' => $recipient->getReferralEarning(),
                ':created_at' => $recipient->getCreatedAt()->format('Y-m-d H:i:s'),
                ':updated_at' => $recipient->getUpdatedAt()->format('Y-m-d H:i:s'),
            );
        }
        $results = $this->database->run($sql, $params);
        if (empty($results))
            return null;

        $recipient->setId($results[0]);
    }
            
}    