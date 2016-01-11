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

    /** @var IntervalEnforcerInterface */
    private $intervalEnforcer;

    public function __construct(
        Connection $connection,
        RecipientRepository $recipientRepository,
        RewardProviderInterface $rewardProvider,
        PaytoshiApiInterface $api,
        IntervalEnforcerInterface $intervalEnforcer
    ) {
        $this->connection = $connection;
        $this->recipientRepository = $recipientRepository;
        $this->rewardProvider = $rewardProvider;
        $this->api = $api;
        $this->intervalEnforcer = $intervalEnforcer;
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

            // Waiting interval check
            $interval = $this->intervalEnforcer->check($ip, $recipient);
            if ($interval) {
                throw new \Exception(
                    sprintf('You can get a reward again in %s.', $interval->format('%Hh, %Im, %Ss'))
                );
            }

            // Reward generation
            $earning = $this->rewardProvider->getReward();

            // Payout creation
            $payout = new Payout();
            $payout->setIp($ip);
            $payout->setRecipientAddress($recipient->getAddress());
            $payout->setEarning($earning);

            // TODO: payment process


            //$this->connection->commit();
            return $payout;

        } catch (\Exception $e) {
            $this->connection->rollBack();
            throw $e;
        }
    }
}