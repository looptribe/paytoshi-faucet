<?php

namespace Looptribe\Paytoshi\Templating;

interface ThemeProviderInterface
{
    /**
     * Returns the list of available themes
     *
     * @return array
     */
    public function getList();

    /**
     * Get a theme's template
     *
     * @param string $templateName
     * @return string
     */
    public function getTemplate($templateName);

    /**
     * Get the current theme
     *
     * @return string
     */
    public function getCurrent();
}