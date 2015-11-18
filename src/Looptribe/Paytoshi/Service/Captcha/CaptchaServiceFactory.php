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
        switch($name)
        {
            case 'solve_media':
                return $this->app->SolveMediaService;
            case 'recaptcha':
                return $this->app->RecaptchaService;
            case 'funcaptcha':
                return $this->app->FuncaptchaService;
            default:
                throw new \RuntimeException();
        }
    }
}
