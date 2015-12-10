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

namespace Looptribe\Paytoshi\Service;

use Buzz\Browser;
use Buzz\Message\Response;
use Exception;
use Looptribe\Paytoshi\Exception\PaytoshiException;
use Looptribe\Paytoshi\Model\SettingRepository;

class ApiService
{
    protected $config;
    /** @var  SettingRepository */
    protected $settingRepository;

    public function __construct($options)
    {
        $this->settingRepository = $options['settingRepository'];
        $this->config = $options['config'];
    }

    public function send($address, $amount, $ip, $referral = false)
    {
        $apiKey = $this->settingRepository->getApiKey();
        $query = http_build_query(array('apikey' => $apiKey));
        $url = $this->config['api_url'] . '?' . $query;
        $headers = array(
            'Connection' => 'close'
        );
        $data = http_build_query(array(
            'address' => $address,
            'amount' => $amount,
            'referral' => $referral,
            'ip' => $ip
        ));

        $browser = new Browser();
        $browser->getClient()->setVerifyPeer(false);
        /** @var Response $response */
        try {
            $response = $browser->post($url, $headers, $data);
        } catch (Exception $e) {
            throw new PaytoshiException('Error while posting request', 500, $e);
        }

        $content = json_decode($response->getContent(), true);
        $apiResponse = new ApiResponse($response->isSuccessful(), $response);

        if (!$response->isSuccessful()) {

            if (isset($content['code'])) {
                switch ($content['code']) {
                    case 'NOT_ENOUGH_FUNDS':
                        $apiResponse->setError('Insufficient funds.');
                        break;
                    case 'INVALID_ADDRESS':
                        $apiResponse->setError('Invalid address.');
                        break;
                    case 'FAUCET_DISABLED':
                        $apiResponse->setError('This faucet has been disabled by the owner or the Paytoshi staff.');
                        break;
                    case 'ACCESS_DENIED':
                        $apiResponse->setError('Access denied, please check your apikey.');
                        break;
                    default:
                        $apiResponse->setError(sprintf("Generic error: %s.", $content['code']));
                        break;
                }
            }
            else {
                $apiResponse->setError('Generic error.');
            }
            return $apiResponse;
        }

        $apiResponse->setAmount($content['amount']);
        $apiResponse->setRecipient($content['recipient']);
        return $apiResponse;
    }
}