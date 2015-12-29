<?php

namespace Looptribe\Paytoshi\Logic;

use Looptribe\Paytoshi\Model\Recipient;

interface IntervalEnforcerInterface
{
    function check($ip, Recipient $recipient);
}