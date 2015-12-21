<?php

namespace Looptribe\Paytoshi\Logic;

use Doctrine\DBAL\Connection;
use Doctrine\DBAL\ConnectionException;
use Looptribe\Paytoshi\Api\PaytoshiApiInterface;
use Looptribe\Paytoshi\Model\Payout;
use Looptribe\Paytoshi\Model\Recipient;
use Looptribe\Paytoshi\Model\RecipientRepository;

class RewardLogic
{
    /** @var Connection */
    private $connection;

    /** @var RecipientRepository */
    private $recipientRepository;

    /** @var RewardProviderInterface */
    private $rewardProvider;

    /** @var PaytoshiApiInterface */
    private $api;

    public function __construct(
        Connection $connection,
        RecipientRepository $recipientRepository,
        RewardProviderInterface $rewardProvider,
        PaytoshiApiInterface $api
    ) {
        $this->connection = $connection;
        $this->recipientRepository = $recipientRepository;
        $this->rewardProvider = $rewardProvider;
        $this->api = $api;
    }

    /**
     * @param string $address
     * @param string $ip
     * @param string $challenge
     * @param string $response
     * @param string|null $referralAddress
     * @throws ConnectionException
     * @throws \Exception
     */
    public function create($address, $ip, $challenge, $response, $referralAddress = null)
    {
        // TODO: Captcha Check
        //

        $this->connection->beginTransaction();
        try {

            $recipient = $this->recipientRepository->findOneByAddress($address);
            if (!$recipient) {
                $recipient = new Recipient();
                $recipient->setAddress($address);
            }

            // TODO: timeout check

            // Reward generation
            $earning = $this->rewardProvider->getReward();

            // Payout creation
            $payout = new Payout();
            $payout->setIp($ip);
            $payout->setRecipientAddress($recipient->getAddress());
            $payout->setEarning($earning);

            // TODO: payment process

            //$this->connection->commit();
        } catch (\Exception $e) {
            $this->connection->rollBack();
            throw $e;
        }
    }
}