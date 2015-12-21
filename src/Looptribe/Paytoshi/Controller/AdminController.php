<?php

namespace Looptribe\Paytoshi\Controller;

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

    public function __construct(
        TemplatingEngineInterface $templating,
        UrlGeneratorInterface $urlGenerator,
        SettingsRepository $settingsRepository,
        ThemeProviderInterface $themeProvider
    ) {
        $this->templating = $templating;
        $this->settingsRepository = $settingsRepository;
        $this->urlGenerator = $urlGenerator;
        $this->themeProvider = $themeProvider;
    }

    public function action()
    {
        $params = array_merge($this->getView(), array(
            'themes' => $this->themeProvider->getList(),
        ));
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
            'rewards' => $this->parseRewards($this->settingsRepository->get('rewards')),
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

    /**
     * @param string $rewards
     * @return array
     */
    private function parseRewards($rewards)
    {
        if (empty($rewards)) {
            return array();
        }

        $rewards = explode(',', $rewards);
        $sortedRewards = array();
        foreach ($rewards as $reward) {
            $data = explode('*', $reward);
            $sortedRewards[] = array(
                'amount' => intval($data[0]),
                'probability' => isset($data[1]) ? round(floatval($data[1]), 2) : 1
            );
            usort($sortedRewards, function ($a, $b) {
                return $a['amount'] < $b['amount'] ? -1 : 1;
            });
        }
        return $sortedRewards;
    }

    public function saveAction(Request $request)
    {
        $data = $request->request->all();
        $data['rewards'] = $this->serializeRewards($data['rewards']);
        try {
            $this->settingsRepository->setAll($data);
        } catch (\Exception $ex) {
        }
        return new RedirectResponse($this->urlGenerator->generate('admin'));
    }

    /**
     * @param array $rewards
     * @return string
     */
    private function serializeRewards($rewards)
    {
        if (empty($rewards)) {
            return '';
        }

        //Unpack amount-probability couples
        $rewardArray = array_map(function ($i) {
            if (!isset($i['amount']) || !isset($i['probability'])) {
                return '';
            }
            return sprintf("%s*%s", $i['amount'], $i['probability']);
        }, $rewards);

        //Remove empty values
        $rewardArray = array_filter($rewardArray);

        return implode(',', $rewardArray);
    }
}
