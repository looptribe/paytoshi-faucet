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

class SolveMediaService implements CaptchaServiceInterface {
    const ADCOPY_API_SERVER = 'http://api.solvemedia.com';
    const ADCOPY_API_SECURE_SERVER = 'https://api-secure.solvemedia.com';
    const ADCOPY_VERIFY_SERVER = 'http://verify.solvemedia.com/papi/verify';
    const ADCOPY_SIGNUP = 'http://api.solvemedia.com/public/signup';
    
    protected $app;
    protected $settingRepository;
    
    private $publicKey;
    private $privateKey;
    private $hashKey;
    
    private $useSSL;
    
    public function __construct($app, $settingRepository) {
        $this->app = $app;
        $this->settingRepository = $settingRepository;
        
        $this->publicKey = $this->settingRepository->getSolveMediaChallengeKey();
        $this->privateKey = $this->settingRepository->getSolveMediaVerificationKey();
        $this->hashKey = $this->settingRepository->getSolveMediaAuthenticationKey();
        
        $this->useSSL = false;
    }
    
    public function getServer() {
        return $this->useSSL ? self::ADCOPY_API_SECURE_SERVER : self::ADCOPY_API_SERVER;
    }
    
    public function getPublicKey() {
        return $this->publicKey;
    }
    
    public function getName() {
        return 'solve_media';
    }
    
    public function getChallengeName() {
        return 'adcopy_challenge';
    }
    
    public function getResponseName() {
        return 'adcopy_response';
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
            'User-Agent' => 'solvemedia/PHP',
            'Content-Type' => 'application/x-www-form-urlencoded'
        );
        
        $content = array(
            'privatekey' => $this->privateKey,
            'remoteip'   => $remoteIp,
            'challenge'  => $challenge,
            'response'   => $response
        );
        
        $browser = new Browser();
        $resp = $browser->post(self::ADCOPY_VERIFY_SERVER, $headers, http_build_query($content));
        
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
        
        $hashMaterial = $answers[0] . $challenge . $this->hashKey;
        $hash = sha1($hashMaterial);
        if ($hash != $answers[2])
            throw new CaptchaException('Hash verification error');
        
        return new CaptchaResponse($success);
    }
}
