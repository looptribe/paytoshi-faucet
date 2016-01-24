<?php

namespace Looptribe\Paytoshi\Captcha;

use Buzz\Browser;
use Looptribe\Paytoshi\Captcha\Funcaptcha\FuncaptchaProvider;
use Looptribe\Paytoshi\Captcha\Recaptcha\RecaptchaProvider;
use Looptribe\Paytoshi\Model\SettingsRepository;

class CaptchaProviderFactory
{
    /** @var Browser */
    private $browser;
    /** @var SettingsRepository */
    private $settingsRepository;

    public function __construct(Browser $browser, SettingsRepository $settingsRepository)
    {
        $this->browser = $browser;
        $this->settingsRepository = $settingsRepository;
    }

    public function create($service)
    {
        switch ($service) {
            case 'funcaptcha':
                return new FuncaptchaProvider($this->browser, $this->settingsRepository->get('funcaptcha_public_key'), $this->settingsRepository->get('funcaptcha_private_key'));
            case 'recaptcha':
                return new RecaptchaProvider($this->browser, $this->settingsRepository->get('recaptcha_public_key'), $this->settingsRepository->get('recaptcha_private_key'));
            default:
                throw new \RuntimeException('Invalid captcha provider ' . $service);
        }
    }
}