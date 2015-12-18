<?php
/**
 * Created by PhpStorm.
 * User: Diego
 * Date: 18/12/2015
 * Time: 11.25
 */

namespace Looptribe\Paytoshi\Controller;


use Looptribe\Paytoshi\Model\SettingsRepository;
use Looptribe\Paytoshi\Templating\TemplatingEngineInterface;
use Looptribe\Paytoshi\Templating\ThemeProviderInterface;

class FaqController
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

    public function action()
    {
        return $this->templating->render($this->themeProvider->getTemplate('faq.html.twig'), $this->getTemplateData());
    }

    private function getTemplateData()
    {
        return array(
            'name' => $this->settingsRepository->get('name'),
            'description' => $this->settingsRepository->get('description'),
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