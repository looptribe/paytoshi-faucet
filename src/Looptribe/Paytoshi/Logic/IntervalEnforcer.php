<?php

namespace Looptribe\Paytoshi\Logic;

use Looptribe\Paytoshi\Model\PayoutRepository;
use Looptribe\Paytoshi\Model\Recipient;

class IntervalEnforcer
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
        $lastPayout = $this->payoutRepository->findLastByRecipientAndIp($ip, $recipient);
        $now = new \DateTime();
        if (!$lastPayout) {
            return null;
        }

        $now = new \DateTime();
        $nextPayoutTime = $lastPayout->getCreatedAt()->add(new \DateInterval('PT'.$this->waitingInterval.'S'));
        if ($nextPayoutTime <= $now) {
            return null;
        }

        $interval = $nextPayoutTime->diff($now);
        if (!$interval) {
            throw new \Exception('Wrong interval format');
        }

        return $interval;
    }
}