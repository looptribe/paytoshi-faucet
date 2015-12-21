<?php

namespace Looptribe\Paytoshi\Api;

interface PaytoshiApiInterface
{
    /**
     * @param string $apikey
     * @param string $address
     * @param int $amount
     * @param string $ip
     * @param bool|false $referral
     * @return SendApiResponse
     */
    public function send($apikey, $address, $amount, $ip, $referral = false);
}
