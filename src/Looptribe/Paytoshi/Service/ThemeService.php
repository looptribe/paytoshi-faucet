<?php

/**
 * Paytoshi Faucet Script
 *
 * Contact: info@paytoshi.org
 *
 * @author: Looptribe
 * @link: https://paytoshi.org
 * @package: Looptribe\Paytoshi
 */

namespace Looptribe\Paytoshi\Service;

use Looptribe\Paytoshi\Exception\PaytoshiException;

class ThemeService
{

    protected $settingRepository;
    protected $config;

    public function __construct($options)
    {
        $this->settingRepository = $options['settingRepository'];
        $this->config = $options['config'];
    }

    public function getThemes()
    {
        $templateDir = $this->config['template_path'];
        $themes = array();
        $path = '.' . DIRECTORY_SEPARATOR . $templateDir . DIRECTORY_SEPARATOR;
        foreach (glob($path . '*', GLOB_ONLYDIR) as $dir) {
            $themes[] = str_replace($path, '', $dir);
        }
        return $themes;
    }

    public function getTemplate($filename)
    {
        $templateDir = $this->config['template_path'];
        //Before setup there is no theme...
        if ($this->settingRepository) {
            $currentTheme = $this->settingRepository->getTheme();
            $filePath = '.' . DIRECTORY_SEPARATOR . $templateDir . DIRECTORY_SEPARATOR . $currentTheme . DIRECTORY_SEPARATOR . $filename;
            if (is_file($filePath) && is_readable($filePath)) {
                return $currentTheme . DIRECTORY_SEPARATOR . $filename;
            }
        }

        $defaultTheme = $this->config['default_theme'];
        $defaultFilePath = '.' . DIRECTORY_SEPARATOR . $templateDir . DIRECTORY_SEPARATOR . $defaultTheme . DIRECTORY_SEPARATOR . $filename;
        if (is_file($defaultFilePath) && is_readable($defaultFilePath)) {
            return $defaultTheme . DIRECTORY_SEPARATOR . $filename;
        }

        throw new PaytoshiException('Unable to load a theme.');
    }
}
