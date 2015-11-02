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

class CaptchaServiceFactory
{
    protected $app;

    public function __construct($app)
    {
        $this->app = $app;
    }

    public function getService($name)
    {
        if ($name === 'solve_media') {
            return $this->app->SolveMediaService;
        } else {
            if ($name === 'recaptcha') {
                return $this->app->RecaptchaService;
            }
        }

        throw RuntimeException();
    }
}
