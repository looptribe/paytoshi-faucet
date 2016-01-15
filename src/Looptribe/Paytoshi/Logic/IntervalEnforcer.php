<?php

namespace Looptribe\Paytoshi\Logic;

use Looptribe\Paytoshi\Model\PayoutRepository;
use Looptribe\Paytoshi\Model\Recipient;

class IntervalEnforcer implements IntervalEnforcerInterface
{
    /** @var PayoutRepository */
    private $payoutRepository;

    /** @var integer */
    private $waitingInterval;

    public function __construct(PayoutRepository $payoutRepository, $waitingInterval)
    {
        $this->payoutRepository = $payoutRepository;
        $this->waitingInterval = $waitingInterval;
    }

    /**
     * @param $ip
     * @param Recipient $recipient
     * @return \DateInterval|null
     * @throws \Exception
     */
    public function check($ip, Recipient $recipient)
    {
        if ($this->waitingInterval < 0) {
            throw new \Exception('Invalid waiting interval');
        }

        $lastPayout = $this->payoutRepository->findLastByRecipientAndIp($ip, $recipient);
        if (!$lastPayout) {
            return null;
        }

        $now = new \DateTime();
        try {
            $interval = new \DateInterval('PT'.$this->waitingInterval.'S');
        }
        catch (\Exception $e) {
            throw new \Exception('Invalid waiting interval format');
        }

        $nextPayoutTime = $lastPayout->getCreatedAt()->add($interval);
        if ($nextPayoutTime <= $now) {
            return null;
        }

        return $nextPayoutTime->diff($now);
    }
}