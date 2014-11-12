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

class ApiResponse {
    private $success;
    private $message;
    private $response;
            
    public function __construct($success, $message = null, $response = null) {
        $this->success = $success;
        $this->message = $message;
        $this->response = $response;
    }
    
    public function getSuccess() {
        return $this->success;
    }
    
    public function getMessage() {
        return $this->message;
    }
    
    public function getResponse() {
        return $this->response;
    }
}
