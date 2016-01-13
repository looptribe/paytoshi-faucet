<?php

namespace Looptribe\Paytoshi\Controller;

use Looptribe\Paytoshi\Api\PaytoshiApiInterface;
use Looptribe\Paytoshi\Logic\RewardMapper;
use Looptribe\Paytoshi\Model\SettingsRepository;
use Looptribe\Paytoshi\Templating\TemplatingEngineInterface;
use Looptribe\Paytoshi\Templating\ThemeProviderInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class AdminController
{
    /** @var TemplatingEngineInterface */
    private $templating;

    /** @var SettingsRepository */
    private $settingsRepository;

    /** @var UrlGeneratorInterface */
    private $urlGenerator;

    /** @var ThemeProviderInterface */
    private $themeProvider;

    /** @var PaytoshiApiInterface */
    private $api;

    /** @var RewardMapper */
    private $rewardMapper;

    public function __construct(
        TemplatingEngineInterface $templating,
        UrlGeneratorInterface $urlGenerator,
        SettingsRepository $settingsRepository,
        ThemeProviderInterface $themeProvider,
        PaytoshiApiInterface $paytoshi,
        RewardMapper $rewardMapper
    ) {
        $this->templating = $templating;
        $this->settingsRepository = $settingsRepository;
        $this->urlGenerator = $urlGenerator;
        $this->themeProvider = $themeProvider;
        $this->paytoshi = $paytoshi;
        $this->rewardMapper = $rewardMapper;
    }

    public function action()
    {
        $params = array_merge($this->getView(), array(
            'themes' => $this->themeProvider->getList(),
            'api_key_ok' => false,
            'available_balance' => 0,
        ));
        try {
            $balance = $this->paytoshi->getBalance($this->settingsRepository->get('api_key'));
            $params['api_key_ok'] = $balance->isSuccessful();
            $params['available_balance'] = $balance->getAvailableBalance();
        }
        catch (\Exception $ex) {
        }

        return $this->templating->render('admin/admin.html.twig', $params);
    }

    private function getView()
    {
        return array(
            'version' => $this->settingsRepository->get('version'),
            'api_key' => $this->settingsRepository->get('api_key'),
            'name' => $this->settingsRepository->get('name'),
            'description' => $this->settingsRepository->get('description'),
            'theme' => $this->settingsRepository->get('theme'),
            'captcha_provider' => $this->settingsRepository->get('captcha_provider'),
            'solve_media_challenge_key' => $this->settingsRepository->get('solve_media_challenge_key'),
            'solve_media_verification_key' => $this->settingsRepository->get('solve_media_verification_key'),
            'solve_media_authentication_key' => $this->settingsRepository->get('solve_media_authentication_key'),
            'recaptcha_public_key' => $this->settingsRepository->get('recaptcha_public_key'),
            'recaptcha_private_key' => $this->settingsRepository->get('recaptcha_private_key'),
            'funcaptcha_public_key' => $this->settingsRepository->get('funcaptcha_public_key'),
            'funcaptcha_private_key' => $this->settingsRepository->get('funcaptcha_private_key'),
            'waiting_interval' => $this->settingsRepository->get('waiting_interval'),
            'rewards' => $this->rewardMapper->stringToArray($this->settingsRepository->get('rewards')),
            'referral_percentage' => $this->settingsRepository->get('referral_percentage'),
            'custom_css' => $this->settingsRepository->get('custom_css'),
            'content_header_box' => $this->settingsRepository->get('content_header_box'),
            'content_left_box' => $this->settingsRepository->get('content_left_box'),
            'content_right_box' => $this->settingsRepository->get('content_right_box'),
            'content_center1_box' => $this->settingsRepository->get('content_center1_box'),
            'content_center2_box' => $this->settingsRepository->get('content_center2_box'),
            'content_center3_box' => $this->settingsRepository->get('content_center3_box'),
            'content_footer_box' => $this->settingsRepository->get('content_footer_box'),
        );
    }

    public function saveAction(Request $request)
    {
        $data = $request->request->all();
        $rewards = isset($data['rewards']) ? $data['rewards'] : array();
        $data['rewards'] = $this->rewardMapper->arrayToString($rewards);
        try {
            $this->settingsRepository->setAll($data);
        } catch (\Exception $ex) {
        }
        return new RedirectResponse($this->urlGenerator->generate('admin'));
    }

}
