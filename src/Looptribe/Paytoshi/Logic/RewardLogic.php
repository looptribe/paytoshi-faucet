<?php

namespace Looptribe\Paytoshi\Logic;

use Doctrine\DBAL\Connection;
use Doctrine\DBAL\ConnectionException;
use Looptribe\Paytoshi\Api\PaytoshiApiInterface;
use Looptribe\Paytoshi\Captcha\CaptchaProviderException;
use Looptribe\Paytoshi\Captcha\CaptchaProviderInterface;
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

    /** @var CaptchaProviderInterface */
    private $captchaProvider;

    public function __construct(
        Connection $connection,
        RecipientRepository $recipientRepository,
        RewardProviderInterface $rewardProvider,
        PaytoshiApiInterface $api,
        IntervalEnforcerInterface $intervalEnforcer,
        CaptchaProviderInterface $captchaProvider
    ) {
        $this->connection = $connection;
        $this->recipientRepository = $recipientRepository;
        $this->rewardProvider = $rewardProvider;
        $this->api = $api;
        $this->intervalEnforcer = $intervalEnforcer;
        $this->captchaProvider = $captchaProvider;
    }

    /**
     * @param string $address
     * @param string $ip
     * @param string $challenge
     * @param string $response
     * @param string|null $referralAddress
     * @return Payout
     * @throws ConnectionException
     * @throws \Exception
     */
    public function create($address, $ip, $challenge, $response, $referralAddress = null)
    {
        try {
            $options = array (
                'challenge' => $challenge,
                'response' => $response,
                'ip' => $ip
            );
            $captchaResponse = $this->captchaProvider->checkAnswer($options);
        }
        catch (CaptchaProviderException $e) {
            throw new \Exception(
                sprintf('Captcha error: %s', $e->getMessage())
            );
        }

        if (!$captchaResponse->isSuccessful()) {
            throw new \Exception($captchaResponse->getMessage());
        }

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