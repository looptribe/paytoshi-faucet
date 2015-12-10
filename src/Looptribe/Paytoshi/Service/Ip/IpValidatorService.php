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

namespace Looptribe\Paytoshi\Service\Ip;

class IpValidatorService
{
    /**
     * Check that a given string is a valid IP address
     *
     * @param  string $ip
     * @return boolean
     */
    public function validate($ip)
    {
        $flags = FILTER_FLAG_IPV4;# | FILTER_FLAG_IPV6;
        if (filter_var($ip, FILTER_VALIDATE_IP, $flags) === false) {
            return false;
        }

        return true;
    }
}