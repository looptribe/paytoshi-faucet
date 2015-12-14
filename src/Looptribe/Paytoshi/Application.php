<?php

namespace Looptribe\Paytoshi;

use Looptribe\Paytoshi\Controller\IndexController;
use Looptribe\Paytoshi\Controller\PublicControllerProvider;
use Silex\Provider\ServiceControllerServiceProvider;

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

        $app->register(new ServiceControllerServiceProvider());

        $app['controller.index'] = $app->share(function () use ($app) {
            return new IndexController();
        });

        $app->mount('/', new PublicControllerProvider());
        //$this->mount('/admin', new AdminControllerProvider());
    }
}
