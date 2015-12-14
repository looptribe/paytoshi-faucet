<?php

namespace Looptribe\Paytoshi\Controller;

use Looptribe\Paytoshi\Templating\TemplatingEngineInterface;

class IndexController
{
    /** @var TemplatingEngineInterface */
    private $templating;

    public function __construct(TemplatingEngineInterface $templating)
    {
        $this->templating = $templating;
    }

    public function action()
    {
        return $this->templating->render('default/layout.html.twig');
    }
}
