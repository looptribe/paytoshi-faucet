<?php

namespace Looptribe\Paytoshi\Api;

use Buzz\Browser;

class PaytoshiApi implements PaytoshiApiInterface
{
    /** @var Browser */
    private $browser;

    /** @var string */
    private $baseUrl;

    public function __construct(Browser $browser, $baseUrl)
    {
        $this->browser = $browser;
        $this->baseUrl = $baseUrl;
    }

    /**
     * @inheritdoc
     */
    public function send($apikey, $address, $amount, $ip, $referral = false)
    {
        $url = $this->baseUrl . 'faucet/send?' . http_build_query(array('apikey' => $apikey));
        $headers = array(
            'Connection' => 'close',
        );
        $data = http_build_query(array(
            'address' => $address,
            'amount' => $amount,
            'referral' => $referral,
            'ip' => $ip
        ));

        $response = $this->browser->post($url, $headers, $data);

        return new SendApiResponse($response);
    }
}
