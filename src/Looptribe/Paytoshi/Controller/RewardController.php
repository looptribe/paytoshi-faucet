<?php

namespace Looptribe\Paytoshi\Controller;

use Looptribe\Paytoshi\Captcha\CaptchaProviderInterface;
use Looptribe\Paytoshi\Model\SettingsRepository;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class RewardController
{
    /** @var SettingsRepository */
    private $settingsRepository;
    /** @var CaptchaProviderInterface */
    private $captchaProvider;
    /** @var UrlGeneratorInterface */
    private $urlGenerator;

    public function __construct(SettingsRepository $settingsRepository, CaptchaProviderInterface $captchaProvider, UrlGeneratorInterface $urlGenerator)
    {
        $this->settingsRepository = $settingsRepository;
        $this->captchaProvider = $captchaProvider;
        $this->urlGenerator = $urlGenerator;
    }

    public function action(Request $request)
    {
        return new RedirectResponse($this->urlGenerator->generate('homepage'));
    }
}