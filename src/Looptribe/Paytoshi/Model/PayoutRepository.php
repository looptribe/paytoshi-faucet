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

use DateTime;
use Looptribe\Paytoshi\Service\DatabaseService;

class PayoutRepository
{
    /* @var $database DatabaseService */
    private $database;
    const TABLE_NAME = 'paytoshi_payouts';

    public function __construct($database)
    {
        $this->database = $database;
    }

    public function findLastByRecipientAndIp($recipient, $remoteIp)
    {
        $recipientCondition = '';
        $params = array(
            ':ip' => $remoteIp
        );
        if ($recipient->getId()) {
            $recipientCondition = 'OR recipient_address = :address';
            $params[':address'] = $recipient->getAddress();
        }
        $sql = sprintf("SELECT id, recipient_address, ip, created_at FROM %s "
            . "WHERE ip = :ip %s ORDER BY created_at DESC LIMIT 1", self::TABLE_NAME, $recipientCondition);
        $results = $this->database->run($sql, $params);
        if (empty($results)) {
            return null;
        }

        $data = $results[0];

        $payout = new Payout();
        $payout->setId($data['id']);
        $payout->setRecipientAddress($data['recipient_address']);
        $payout->setCreatedAt(new DateTime($data['created_at']));

        return $payout;
    }

    public function save(Payout $payout)
    {
        $sql = sprintf("INSERT INTO %s (recipient_address, referral_recipient_address, earning, referral_earning, "
            . "ip, created_at)  "
            . "VALUES (:recipient_address, :referral_recipient_address, :earning, :referral_earning, :ip, :created_at)",
            self::TABLE_NAME);
        $results = $this->database->run($sql, array(
            ':recipient_address' => $payout->getRecipientAddress(),
            ':referral_recipient_address' => $payout->getReferralRecipientAddress(),
            ':earning' => $payout->getEarning(),
            ':referral_earning' => $payout->getReferralEarning(),
            ':ip' => $payout->getIp(),
            ':created_at' => $payout->getCreatedAt()->format('Y-m-d H:i:s'),
        ));
        if (empty($results)) {
            return null;
        }

        $payout->setId($results[0]);
    }
}
