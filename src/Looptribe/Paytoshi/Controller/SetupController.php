<?php

namespace Looptribe\Paytoshi\Controller;

use Looptribe\Paytoshi\Templating\TemplatingEngineInterface;

class SetupController
{
    /** @var TemplatingEngineInterface */
    private $templating;

    public function __construct(TemplatingEngineInterface $templating)
    {
        $this->templating = $templating;
    }

    public function startAction()
    {
        return $this->templating->render('default/setup.html.twig');
    }

    public function action()
    {

    }
}
