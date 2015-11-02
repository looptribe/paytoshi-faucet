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
    const SERVER = 'https://www.google.com';
    const VERIFY_SERVER = 'https://www.google.com/recaptcha/api/siteverify';
    
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
        return '';
    }
    
    public function getResponseName() {
        return 'g-recaptcha-response';
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
            'secret' => $this->privateKey,
            'remoteip'   => $remoteIp,
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
        
        $answer = json_decode($resp->getContent());
        if (!$answer)
            throw new CaptchaException('Error: Invalid captcha response');

        $success = isset($answer->success) && filter_var($answer->success, FILTER_VALIDATE_BOOLEAN);
        if (!$success)
            return new CaptchaResponse($success, 'Invalid captcha');
        
        return new CaptchaResponse($success);
    }
}
