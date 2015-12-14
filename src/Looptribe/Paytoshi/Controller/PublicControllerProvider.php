<?php

namespace Looptribe\Paytoshi\Controller;

use Silex\Application;
use Silex\ControllerCollection;
use Silex\ControllerProviderInterface;

class PublicControllerProvider implements ControllerProviderInterface
{
    /**
     * @inheritdoc
     */
    public function connect(Application $app)
    {
        /** @var ControllerCollection $controllers */
        $controllers = $app['controllers_factory'];

        $controllers->get('/', 'controller.index:action');

        return $controllers;
    }
}
