<?php

namespace Looptribe\Paytoshi\Controller;

use Looptribe\Paytoshi\Captcha\CaptchaProviderInterface;
use Looptribe\Paytoshi\Logic\RewardLogic;
use Looptribe\Paytoshi\Model\SettingsRepository;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\Flash\FlashBagInterface;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class RewardController
{
    /** @var SettingsRepository */
    private $settingsRepository;
    /** @var CaptchaProviderInterface */
    private $captchaProvider;
    /** @var UrlGeneratorInterface */
    private $urlGenerator;
    /** @var RewardLogic */
    private $rewardLogic;
    /** @var FlashBagInterface */
    private $flashBag;

    public function __construct(
        SettingsRepository $settingsRepository,
        CaptchaProviderInterface $captchaProvider,
        UrlGeneratorInterface $urlGenerator,
        RewardLogic $rewardLogic,
        FlashBagInterface $flashBag
    ) {
        $this->settingsRepository = $settingsRepository;
        $this->captchaProvider = $captchaProvider;
        $this->urlGenerator = $urlGenerator;
        $this->rewardLogic = $rewardLogic;
        $this->flashBag = $flashBag;
    }

    public function action(Request $request)
    {
        $address = $request->get('address');
        if (empty($address)) {
            $this->flashBag->add('warning', 'Missing address');

            return new RedirectResponse($this->urlGenerator->generate('homepage'));
        }

        $challenge = null;
        if ($this->captchaProvider->getChallengeName()) {
            $challenge = $request->get($this->captchaProvider->getChallengeName());
            if (empty($address)) {
                $this->flashBag->add('warning', 'Missing captcha');

                return new RedirectResponse($this->urlGenerator->generate('homepage'));
            }
        }

        if ($this->captchaProvider->getResponseName()) {
            $response = $request->get($this->captchaProvider->getResponseName());
            if (empty($response)) {
                $this->flashBag->add('warning', 'Missing captcha');

                return new RedirectResponse($this->urlGenerator->generate('homepage'));
            }
        }

        // TODO: check ip detection
        $ip = $request->getClientIp();
        $referralAddress = $request->get('r');

        $this->rewardLogic->create($address, $ip, $challenge, $response, $referralAddress);

        return new RedirectResponse($this->urlGenerator->generate('homepage'));
    }
}