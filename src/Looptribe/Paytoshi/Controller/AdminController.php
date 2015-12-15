<?php

namespace Looptribe\Paytoshi\Controller;

use Looptribe\Paytoshi\Model\SettingsRepository;
use Looptribe\Paytoshi\Templating\TemplatingEngineInterface;

class AdminController
{
    /** @var TemplatingEngineInterface */
    private $templating;

    /** @var SettingsRepository */
    private $settingsRepository;

    public function __construct(TemplatingEngineInterface $templating, SettingsRepository $settingsRepository)
    {
        $this->templating = $templating;
        $this->settingsRepository = $settingsRepository;
    }

    public function action()
    {
        $params = array_merge($this->getView(), array(
           'themes' => array('default', 'wide'),
        ));
        return $this->templating->render('default/admin.html.twig', $params);
    }

    private function getView()
    {
        return array(
            'version' => $this->settingsRepository->get('version'),
            'api_key' => $this->settingsRepository->get('api_key'),
            'name' => $this->settingsRepository->get('name'),
            'description' => $this->settingsRepository->get('description'),
            'current_theme' => $this->settingsRepository->get('theme'),
            'captcha_provider' => $this->settingsRepository->get('captcha_provider'),
            'solve_media' => array(
                'challenge_key' => $this->settingsRepository->get('solve_media_challenge_key'),
                'verification_key' => $this->settingsRepository->get('solve_media_verification_key'),
                'authentication_key' => $this->settingsRepository->get('solve_media_authentication_key'),
            ),
            'recaptcha' => array(
                'public_key' => $this->settingsRepository->get('recaptcha_public_key'),
                'private_key' => $this->settingsRepository->get('recaptcha_private_key')
            ),
            'funcaptcha' => array(
                'public_key' => $this->settingsRepository->get('funcaptcha_public_key'),
                'private_key' => $this->settingsRepository->get('funcaptcha_private_key')
            ),
            'waiting_interval' => $this->settingsRepository->get('waiting_interval'),
            'rewards' => $this->parseRewards($this->settingsRepository->get('rewards')),
            'referral_percentage' => $this->settingsRepository->get('referral_percentage'),
            'css' => $this->settingsRepository->get('custom_css'),
            'header_box' => $this->settingsRepository->get('content_header_box'),
            'left_box' => $this->settingsRepository->get('content_left_box'),
            'right_box' => $this->settingsRepository->get('content_right_box'),
            'center1_box' => $this->settingsRepository->get('content_center1_box'),
            'center2_box' => $this->settingsRepository->get('content_center2_box'),
            'center3_box' => $this->settingsRepository->get('content_center3_box'),
            'footer_box' => $this->settingsRepository->get('content_footer_box'),
        );
    }

    private function parseRewards($rewards)
    {
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
}
