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

interface CaptchaServiceInterface {
    function getName();
    function getServer();
    function getPublicKey();
    function checkAnswer($remoteIp, $challenge, $response);
    function getChallengeName();
    function getResponseName();
    
}
