<?php

namespace Looptribe\Paytoshi\Templating;

use Looptribe\Paytoshi\Model\SettingsRepository;

class LocalThemeProvider implements ThemeProviderInterface
{
    /** @var SettingsRepository */
    private $settingsRepository;

    /** @var string */
    private $templateDir;

    public function __construct(SettingsRepository $settingsRepository, $templateDir)
    {
        $this->settingsRepository = $settingsRepository;
        $this->templateDir = $templateDir;
    }

    /**
     * Returns the list of available themes
     *
     * @return array
     */
    public function getList()
    {
        $themes = array();
        foreach (glob($this->getTemplatePath().'*', GLOB_ONLYDIR) as $dir) {
            $themes[] = str_replace($this->getTemplatePath(), '', $dir);
        }
        return $themes;
    }

    /**
     * Get a theme's template
     *
     * @param string $templateName
     * @param string $theme
     * @return string
     */
    public function getTemplate($templateName, $theme = 'default')
    {
        // TODO: Implement getTemplate() method.
    }


    /**
     * Get the current theme
     *
     * @return string
     */
    public function getCurrent()
    {
        return $this->settingsRepository->get('theme', 'default');
    }

    private function getTemplatePath()
    {
        return sprintf(
            '.%s%s%s',
            DIRECTORY_SEPARATOR,
            $this->templateDir,
            DIRECTORY_SEPARATOR

        );
    }
}