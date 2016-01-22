<?php

namespace Looptribe\Paytoshi\Logic;

use Doctrine\DBAL\Connection;
use Looptribe\Paytoshi\Api\PaytoshiApiInterface;
use Looptribe\Paytoshi\Captcha\CaptchaProviderException;
use Looptribe\Paytoshi\Captcha\CaptchaProviderInterface;
use Looptribe\Paytoshi\Model\Payout;
use Looptribe\Paytoshi\Model\PayoutRepository;
use Symfony\Component\Security\Acl\Exception\Exception;

class RewardLogic
{
    /** @var Connection */
    private $connection;

    /** @var RewardProviderInterface */
    private $rewardProvider;

    /** @var PaytoshiApiInterface */
    private $api;

    /** @var IntervalEnforcerInterface */
    private $intervalEnforcer;

    /** @var CaptchaProviderInterface */
    private $captchaProvider;

    /** @var PayoutRepository */
    private $payoutRepository;

    /** @var string */
    private $apikey;

    public function __construct(
        Connection $connection,
        PayoutRepository $payoutRepository,
        RewardProviderInterface $rewardProvider,
        PaytoshiApiInterface $api,
        IntervalEnforcerInterface $intervalEnforcer,
        CaptchaProviderInterface $captchaProvider,
        $apikey
    ) {
        $this->connection = $connection;
        $this->rewardProvider = $rewardProvider;
        $this->api = $api;
        $this->intervalEnforcer = $intervalEnforcer;
        $this->captchaProvider = $captchaProvider;
        $this->payoutRepository = $payoutRepository;
        $this->apikey = $apikey;
    }

    /**
     * @param string $address
     * @param string $ip
     * @param string $challenge
     * @param string $response
     * @param string|null $referralAddress
     * @return RewardLogicResult
     */
    public function create($address, $ip, $challenge, $response, $referralAddress = null)
    {
        $result = new RewardLogicResult();
        $result->setSuccessful(false);

        try {
            $options = array (
                'challenge' => $challenge,
                'response' => $response,
                'ip' => $ip
            );
            $captchaResponse = $this->captchaProvider->checkAnswer($options);
        }
        catch (CaptchaProviderException $e) {
            $result->setSeverity(RewardLogicResult::SEVERITY_DANGER);
            $result->setError($e->getMessage());
            return $result;
        }

        if (!$captchaResponse->isSuccessful()) {
            $result->setSeverity(RewardLogicResult::SEVERITY_WARNING);
            $result->setError($captchaResponse->getMessage());
            return $result;
        }

        $this->connection->beginTransaction();
        try {
            // Waiting interval check
            $interval = null;
            try {
                $interval = $this->intervalEnforcer->check($ip, $address);
            } catch (\Exception $e) {
                $result->setSeverity(RewardLogicResult::SEVERITY_DANGER);
                $result->setError($e->getMessage());
                throw $e;
            }

            if ($interval) {
                $result->setSeverity(RewardLogicResult::SEVERITY_WARNING);
                $result->setError(sprintf('You can get a reward again in %s.', $interval->format('%Hh, %Im, %Ss')));
                throw new Exception('Waiting interval not satisfied');
            }

            // Reward generation
            $earning = $this->rewardProvider->getReward();

            // Payout creation
            $payout = new Payout();
            $payout->setIp($ip);
            $payout->setRecipientAddress($address);
            $payout->setEarning($earning);

            try {
                $response = $this->api->send($this->apikey, $payout->getRecipientAddress(), $payout->getEarning(), $ip);
            } catch (\Exception $e) {
                $result->setSeverity(RewardLogicResult::SEVERITY_DANGER);
                $result->setError(sprintf('Unable to create reward: %s', $e->getMessage()));
                throw $e;
            }

            if (!$response->isSuccessful()) {
                $result->setSeverity(RewardLogicResult::SEVERITY_DANGER);
                $result->setError(sprintf('Unable to create reward: %s', $response->getError()));
                throw new Exception('Reward creation failed');
            }

            $this->payoutRepository->insert($payout);

            $this->connection->commit();

            $result->setSuccessful(true);
            $result->setResponse($response);
        } catch (\Exception $e) {
            $this->connection->rollBack();
        }

        return $result;
    }
}