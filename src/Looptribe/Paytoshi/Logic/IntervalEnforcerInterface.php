<?php

namespace Looptribe\Paytoshi\Logic;

use Looptribe\Paytoshi\Model\Recipient;

interface IntervalEnforcerInterface
{
    /**
     * @param $ip
     * @param Recipient $recipient
     * @return \DateInterval|null
     * @throws \Exception
     */
    function check($ip, Recipient $recipient);
}