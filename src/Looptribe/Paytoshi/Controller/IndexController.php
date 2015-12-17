<?php

namespace Looptribe\Paytoshi\Controller;

use Looptribe\Paytoshi\Model\SettingsRepository;
use Looptribe\Paytoshi\Templating\TemplatingEngineInterface;
use Looptribe\Paytoshi\Templating\ThemeProviderInterface;
use Symfony\Component\HttpFoundation\Request;

class IndexController
{
    /** @var TemplatingEngineInterface */
    private $templating;

    /** @var ThemeProviderInterface */
    private $themeProvider;

    /** @var SettingsRepository */
    private $settingsRepository;

    public function __construct(TemplatingEngineInterface $templating, ThemeProviderInterface $themeProvider, SettingsRepository $settingsRepository)
    {
        $this->templating = $templating;
        $this->themeProvider = $themeProvider;
        $this->settingsRepository = $settingsRepository;
    }

    public function action(Request $request)
    {
        $data = array(
            'referral' => $request->get('r'),
            'address' => $request->getSession()->get('address')
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
            'rewards' => array(),//$this->rewardService->getNormalized(),
            'rewards_average' => 0,//$this->rewardService->getAverage(),
            'rewards_max' => 0,//$this->rewardService->getMax(),
            'waiting_interval' => $this->settingsRepository->get('waiting_interval'),
            'captcha' => array(
                'name' => 'funcaptcha',//$this->captchaService->getName(),
                'server' => '',//$this->captchaService->getServer(),
                'public_key' => ''//$this->captchaService->getPublicKey()
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
