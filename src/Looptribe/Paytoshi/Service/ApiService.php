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
    
    public function send($address, $amount, $notes = '') {
        $apiKey = $this->settingRepository->getApiKey();
        $query = http_build_query(array('apikey' => $apiKey));
        $url = $this->config['api_url'] . '?' . $query;
        $headers = array(
        );
        $content = http_build_query(array(
            'address' => $address,
            'amount' => $amount,
            'notes' => $notes
        ));
        
        $browser = new Browser();
        /* @var $response Response */
        try {
            $response = $browser->post($url, $headers, $content);
        }
        catch (Exception $e) {
            throw new PaytoshiException('Failed to send', 500, $e);
        }
        
        if (!$response->isSuccessful()) {
            $content = $response->getContent();
            $apiResponse = new ApiResponse(false, $response);
            switch ($content['message']) {
                case 'NOT_ENOUGH_FUNDS':
                    $apiResponse->setError('Insufficient funds.');
                    break;
                case 'INVALID_ADDRESS':
                    $apiResponse->setError('Invalid address.');
                    break;
                default:
                    $apiResponse->setError('Failed to send');
                    break;
            }
            return $apiResponse;
        }
        
        $result = json_decode($response->getContent(), true);
        
        $apiResponse = new ApiResponse(true, $response);
        $apiResponse->setAmount($result['amount']);
        $apiResponse->setRecipient($result['recipient']);
        return $apiResponse;
    }
}
