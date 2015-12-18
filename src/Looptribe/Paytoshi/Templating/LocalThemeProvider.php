<?php

namespace Looptribe\Paytoshi\Templating;

use Looptribe\Paytoshi\Model\SettingsRepository;

class LocalThemeProvider implements ThemeProviderInterface
{
    /** @var SettingsRepository */
    private $settingsRepository;

    /** @var string */
    private $templatePath;
    /** @var string */
    private $defaultTheme;

    public function __construct(SettingsRepository $settingsRepository, $templatePath, $defaultTheme)
    {
        $this->settingsRepository = $settingsRepository;
        $this->templatePath = $templatePath;
        $this->defaultTheme = $defaultTheme;
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
     * @return string
     * @throws \Exception
     */
    public function getTemplate($templateName)
    {
        $templateString = sprintf('%s%s%s',
            $this->getCurrent(),
            DIRECTORY_SEPARATOR,
            $templateName
        );
        $filePath = sprintf('%s%s', $this->getTemplatePath(), $templateString);
        if (!(is_file($filePath) && is_readable($filePath)))
            throw new \Exception('Unable to load template (not a file or not readable)');

        return $templateString;
    }


    /**
     * Get the current theme
     *
     * @return string
     */
    public function getCurrent()
    {
        return $this->settingsRepository->get('theme', $this->defaultTheme);
    }

    private function getTemplatePath()
    {
        return sprintf(
            '%s%s',
            $this->templatePath,
            DIRECTORY_SEPARATOR

        );
    }
}