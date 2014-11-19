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

use Buzz\Browser;
use Exception;

class RecaptchaService implements CaptchaServiceInterface {
    const SERVER = 'http://www.google.com';
    const VERIFY_SERVER = 'https://www.google.com/recaptcha/api/verify';
    
    protected $app;
    protected $settingRepository;
    
    private $publicKey;
    private $privateKey;
    private $hashKey;
    
    private $useSSL;
    
    public function __construct($app, $settingRepository) {
        $this->app = $app;
        $this->settingRepository = $settingRepository;
        
        $this->publicKey = $this->settingRepository->getRecaptchaPublicKey();
        $this->privateKey = $this->settingRepository->getRecaptchaPrivateKey();
    }
    
    public function getServer() {
        return self::SERVER;
    }
    
    public function getPublicKey() {
        return $this->publicKey;
    }
    
    public function getName() {
        return 'recaptcha';
    }
    
    public function getChallengeName() {
        return 'recaptcha_challenge_field';
    }
    
    public function getResponseName() {
        return 'recaptcha_response_field';
    }
    
    /**
     * Calls an HTTP POST function to verify if the user's guess was correct
     *
     * @param string $remoteIp
     * @param string $challenge
     * @param string $response
     * @throws Exception
     * @return boolean
     */
    public function checkAnswer($remoteIp, $challenge, $response) {
        if (empty($remoteIp)) 
            throw new CaptchaException('RemoteIp missing');
        
        $headers = array(
        );
        
        $content = array(
            'privatekey' => $this->privateKey,
            'remoteip'   => $remoteIp,
            'challenge'  => $challenge,
            'response'   => $response
        );
        
        $browser = new Browser();
        $browser->getClient()->setVerifyPeer(false);
        
        try {
            $resp = $browser->post(self::VERIFY_SERVER, $headers, http_build_query($content));
        }
        catch (Exception $e) {
            throw $e;
            throw new CaptchaException('Failed to send', 500, $e);
        }
        
        if (!$resp->isSuccessful())
            throw new CaptchaException('Error: ' . $resp->getStatusCode());
        
        /**
         * 0: true|false
         * 1: errorMessage (optional)
         * 2: hash
         */
        $answers = explode("\n", $resp->getContent());
        
        $success = filter_var($answers[0], FILTER_VALIDATE_BOOLEAN);
        if (!$success)
            return new CaptchaResponse($success, $answers[1]);
        
        return new CaptchaResponse($success);
    }
}
