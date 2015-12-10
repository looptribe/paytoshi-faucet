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
use Buzz\Message\Response;
use Exception;
use Looptribe\Paytoshi\Model\SettingRepository;

class FuncaptchaService implements CaptchaServiceInterface {
    const SERVER = 'https://funcaptcha.com';
    const VERIFY_SERVER = 'https://funcaptcha.com/fc/v/';
    
    protected $settingRepository;
    
    private $publicKey;
    private $privateKey;

    public function __construct(SettingRepository $settingRepository) {
        $this->settingRepository = $settingRepository;
        
        $this->publicKey = $this->settingRepository->getFuncaptchaPublicKey();
        $this->privateKey = $this->settingRepository->getFuncaptchaPrivateKey();
    }
    
    public function getServer() {
        return self::SERVER;
    }
    
    public function getPublicKey() {
        return $this->publicKey;
    }
    
    public function getName() {
        return 'funcaptcha';
    }
    
    public function getChallengeName() {
        return '';
    }
    
    public function getResponseName() {
        return 'fc-token';
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
        $headers = array(
            'Connection' => 'close'
        );
        
        $content = array(
            'private_key' => $this->privateKey,
            'session_token'   => $response,
            'simple_mode' => 1
        );

        $browser = new Browser();
        $browser->getClient()->setVerifyPeer(false);
        
        try {
            /** @var Response $resp */
            $resp = $browser->post(self::VERIFY_SERVER, $headers, http_build_query($content));
        }
        catch (Exception $e) {
            throw new CaptchaException('Failed to send captcha', 500, $e);
        }

        if (!$resp->isSuccessful())
            throw new CaptchaException('Error: ' . $resp->getStatusCode());
        
        $answer = $resp->getContent();
        if (!$answer)
            throw new CaptchaException('Error: Invalid captcha response');

        $success = isset($answer) && filter_var($answer, FILTER_VALIDATE_INT);
        if (!$success)
            return new CaptchaResponse($success, 'Invalid captcha');
        
        return new CaptchaResponse($success);
    }
}
