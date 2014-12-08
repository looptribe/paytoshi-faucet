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

class ApiService {
    
    protected $config;
    protected $settingRepository;
    
    public function __construct($options) {
        $this->settingRepository = $options['settingRepository'];
        $this->config = $options['config'];
        
    }
    
    public function send($address, $amount, $ip, $referral = false) {
        $apiKey = $this->settingRepository->getApiKey();
        $query = http_build_query(array('apikey' => $apiKey));
        $url = $this->config['api_url'] . '?' . $query;
        $data = http_build_query(array(
            'address' => $address,
            'amount' => $amount,
            'referral' => $referral,
            'ip' => $ip
        ));
        
        $browser = new Browser();
        $browser->getClient()->setVerifyPeer(false);
        /* @var $response Response */
        try {
            $response = $browser->post($url, array(), $data);
        }
        catch (Exception $e) {
            throw new PaytoshiException('Failed to send', 500, $e);
        }
        
        $content = json_decode($response->getContent(), true);
        $apiResponse = new ApiResponse($response->isSuccessful(), $response);
        
        if (!$response->isSuccessful()) {
            
            if (isset($content['message']))
                switch ($content['message']) {
                    case 'NOT_ENOUGH_FUNDS':
                        $apiResponse->setError('Insufficient funds.');
                        break;
                    case 'INVALID_ADDRESS':
                        $apiResponse->setError('Invalid address.');
                        break;
                    case 'FAUCET_DISABLED':
                        $apiResponse->setError('This faucet has been disabled by the owner or the Paytoshi staff.');
                        break;
                    default:
                        $apiResponse->setError('Failed to send');
                        break;
                }
            else
                $apiResponse->setError('Failed to send');
            return $apiResponse;
        }
        
        $apiResponse->setAmount($content['amount']);
        $apiResponse->setRecipient($content['recipient']);
        return $apiResponse;
    }
}
