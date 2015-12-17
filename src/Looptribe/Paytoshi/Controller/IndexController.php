<?php

namespace Looptribe\Paytoshi\Controller;

use Looptribe\Paytoshi\Templating\TemplatingEngineInterface;
use Looptribe\Paytoshi\Templating\ThemeProviderInterface;

class IndexController
{
    /** @var TemplatingEngineInterface */
    private $templating;

    /** @var ThemeProviderInterface */
    private $themeProvider;

    public function __construct(TemplatingEngineInterface $templating, ThemeProviderInterface $themeProvider)
    {
        $this->templating = $templating;
        $this->themeProvider = $themeProvider;
    }

    public function action()
    {
        return $this->templating->render($this->themeProvider->getTemplate('index.html.twig'));
    }
}
