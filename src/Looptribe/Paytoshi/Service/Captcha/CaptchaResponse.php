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

namespace Looptribe\Paytoshi\Service\Captcha;

class CaptchaResponse {
    private $success;
    private $message;
            
    public function __construct($success, $message = null) {
        $this->success = $success;
        $this->message = $message;
    }
    
    public function getSuccess() {
        return $this->success;
    }
    
    public function getMessage() {
        return $this->message;
    }
}
