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
    
    protected $app;
    protected $settingRepository;
    
    public function __construct($app, $settingRepository) {
        $this->app = $app;
        $this->settingRepository = $settingRepository;
    }
    
    public function send($address, $amount, $notes = '') {
        $apiKey = $this->settingRepository->getApiKey();
        $query = http_build_query(array('apikey' => $apiKey));
        $url = $this->app->config('api_url') . '?' . $query;
        $headers = array(
        );
        $content = http_build_query(array(
            'address' => $address,
            'amount' => $amount,
            'notes' => $notes
        ));
        
        $browser = new Browser();
        /* @var $respoonse Response */
        try {
            $response = $browser->post($url, $headers, $content);
        }
        catch (Exception $e) {
            throw new PaytoshiException('Failed to send', 500, $e);
        }
        
        if (!$response->isSuccessful())
            return new ApiResponse(false, 'Failed to send', $response);
        
        $result = json_decode($response->getContent(), true);
        
        $view = $this->app->view();
        $view->setData(array(
            'amount' => $result['amount'],
            'to' => $result['recipient'],
            'balanceUrl' => $this->app->config('balance_url')
        ));
        
        return new ApiResponse(true, $view->render('Default/balance.html.twig'), $response);
    }
}
