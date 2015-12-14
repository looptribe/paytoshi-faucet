<?php

namespace Looptribe\Paytoshi;

use Looptribe\Paytoshi\Controller\IndexController;
use Looptribe\Paytoshi\Controller\PublicControllerProvider;
use Silex\Provider;

class Application extends \Silex\Application
{
    public function __construct()
    {
        parent::__construct();
        $this->init();
    }

    private function init()
    {
        $app = $this;

        $app->register(new Provider\ServiceControllerServiceProvider());
        $app->register(new Provider\TwigServiceProvider(), array(
            'twig.path' => __DIR__ . '/../../themes',
        ));

        $app['controller.index'] = $app->share(function () use ($app) {
            return new IndexController();
        });

        $app->mount('/', new PublicControllerProvider());
        //$this->mount('/admin', new AdminControllerProvider());
    }
}
