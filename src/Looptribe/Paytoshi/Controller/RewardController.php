<?php

namespace Looptribe\Paytoshi\Controller;

use Looptribe\Paytoshi\Captcha\CaptchaProviderInterface;
use Looptribe\Paytoshi\Logic\RewardLogic;
use Looptribe\Paytoshi\Model\SettingsRepository;
use Looptribe\Paytoshi\Templating\TemplatingEngineInterface;
use Looptribe\Paytoshi\Templating\ThemeProviderInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\Flash\FlashBagInterface;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class RewardController
{
    /** @var TemplatingEngineInterface */
    private $templating;
    /** @var ThemeProviderInterface */
    private $themeProvider;
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
        TemplatingEngineInterface $templatingEngineInterface,
        ThemeProviderInterface $themeProvider,
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
        $this->templating = $templatingEngineInterface;
        $this->themeProvider = $themeProvider;
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
            if (empty($challenge)) {
                $this->flashBag->add('warning', 'Missing captcha');
                return new RedirectResponse($this->urlGenerator->generate('homepage'));
            }
        }

        $response = null;
        if ($this->captchaProvider->getResponseName()) {
            $response = $request->get($this->captchaProvider->getResponseName());
            if (empty($response)) {
                $this->flashBag->add('warning', 'Missing captcha');
                return new RedirectResponse($this->urlGenerator->generate('homepage'));
            }
        }

        $ip = $request->getClientIp();
        $referralAddress = $request->get('r');

        $result = $this->rewardLogic->create($address, $ip, $challenge, $response, $referralAddress);
        if ($result->isSuccessful()) {
            $this->flashBag->add('success', $this->templating->renderView($this->themeProvider->getTemplate('balance.html.twig'), array('amount' => $result->getResponse()->getAmount(), 'address' => $result->getResponse()->getRecipient())));
        }
        else {
            $this->flashBag->add($result->getSeverity(), $result->getError());
        }

        return new RedirectResponse($this->urlGenerator->generate('homepage'));
    }
}