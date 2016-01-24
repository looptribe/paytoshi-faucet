<?php

namespace Looptribe\Paytoshi\Controller;

use Looptribe\Paytoshi\Captcha\CaptchaProviderInterface;
use Looptribe\Paytoshi\Logic\RewardProviderInterface;
use Looptribe\Paytoshi\Model\SettingsRepository;
use Looptribe\Paytoshi\Templating\TemplatingEngineInterface;
use Looptribe\Paytoshi\Templating\ThemeProviderInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\Flash\FlashBagInterface;

class IndexController
{
    /** @var TemplatingEngineInterface */
    private $templating;

    /** @var ThemeProviderInterface */
    private $themeProvider;

    /** @var SettingsRepository */
    private $settingsRepository;

    /** @var FlashBagInterface */
    private $flashBag;

    /** @var CaptchaProviderInterface */
    private $captchaProvider;

    /** @var RewardProviderInterface */
    private $rewardProvider;

    public function __construct(TemplatingEngineInterface $templating, ThemeProviderInterface $themeProvider, SettingsRepository $settingsRepository, FlashBagInterface $flashBag, CaptchaProviderInterface $captchaProvider, RewardProviderInterface $rewardProvider)
    {
        $this->templating = $templating;
        $this->themeProvider = $themeProvider;
        $this->settingsRepository = $settingsRepository;
        $this->flashBag = $flashBag;
        $this->captchaProvider = $captchaProvider;
        $this->rewardProvider = $rewardProvider;
    }

    public function action(Request $request)
    {
        $data = array(
            'referral' => $request->get('r'),
            'address' => $request->getSession()->get('address'),
            'flashbag' => $this->flashBag
        );
        $data = array_merge($data, $this->getTemplateData());
        return $this->templating->render($this->themeProvider->getTemplate('index.html.twig'), $data);
    }

    private function getTemplateData()
    {
        return array(
            'name' => $this->settingsRepository->get('name'),
            'description' => $this->settingsRepository->get('description'),
            'referral_percentage' => $this->settingsRepository->get('referral_percentage'),
            'rewards' => $this->rewardProvider->getNormalized(),
            'rewards_average' => $this->rewardProvider->getAverage(),
            'rewards_max' => $this->rewardProvider->getMax(),
            'waiting_interval' => $this->settingsRepository->get('waiting_interval'),
            'captcha' => array(
                'provider' => $this->settingsRepository->get('captcha_provider'),
                'public_key' => $this->settingsRepository->get(
                    $this->captchaProvider->getPublicKeyName()
                ),
            ),
            'content' => array(
                'header_box' => $this->settingsRepository->get('header_box'),
                'left_box' => $this->settingsRepository->get('left_box'),
                'right_box' => $this->settingsRepository->get('right_box'),
                'center1_box' => $this->settingsRepository->get('center1_box'),
                'center2_box' => $this->settingsRepository->get('center2_box'),
                'center3_box' => $this->settingsRepository->get('center3_box'),
                'footer_box' => $this->settingsRepository->get('footer_box')
            ),
            'theme' => array(
                'name' => $this->themeProvider->getCurrent(),
                'css' => $this->settingsRepository->get('css')
            )
        );
    }

}
