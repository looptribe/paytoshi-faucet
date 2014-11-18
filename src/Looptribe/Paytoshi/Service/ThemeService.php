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

class ThemeService {
    
    protected $settingRepository;
    protected $config;
    
    public function __construct($options) {
        $this->settingRepository = $options['settingRepository'];
        $this->config = $options['config'];
    }
    
    public function getThemes() {
        return array();
    }
    
    public function getTemplate($filename) {
        $currentTheme = $this->settingRepository->getTheme();
        $templateDir = $this->config['template_path'];
        $filePath = './' . $templateDir . DIRECTORY_SEPARATOR . $currentTheme . DIRECTORY_SEPARATOR . $filename;
        if (file_exists($filePath))
            return $currentTheme . DIRECTORY_SEPARATOR . $filename;
        
        $defaultTheme = $this->config['default_theme'];
        $defaultFilePath = './' . $templateDir . DIRECTORY_SEPARATOR . $defaultTheme . DIRECTORY_SEPARATOR . $filename;
        if (file_exists($defaultFilePath))
            return $defaultTheme . DIRECTORY_SEPARATOR . $filename;
        
        throw new PaytoshiException('Unable to load a theme.');
    }
}
