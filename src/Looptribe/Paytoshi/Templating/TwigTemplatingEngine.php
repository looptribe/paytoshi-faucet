<?php

namespace Looptribe\Paytoshi\Templating;

use Symfony\Component\HttpFoundation\Response;

class TwigTemplatingEngine implements TemplatingEngineInterface
{
    /** @var \Twig_Environment */
    private $twig;

    public function __construct(\Twig_Environment $twig)
    {
        $this->twig = $twig;
    }

    public function render($view, array $parameters = array(), Response $response = null)
    {
        if (null === $response) {
            $response = new Response();
        }
        $response->setContent($this->renderView($view, $parameters));

        return $response;
    }

    public function renderView($view, array $parameters = array())
    {
        return $this->twig->render($view, $parameters);
    }
}
