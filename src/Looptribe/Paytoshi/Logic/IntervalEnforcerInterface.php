<?php

namespace Looptribe\Paytoshi\Logic;

interface IntervalEnforcerInterface
{
    /**
     * @param $ip
     * @param string $address
     * @return \DateInterval|null
     */
    function check($ip, $address);
}