<?php

namespace Looptribe\Paytoshi\Controller;

use Silex\Application;
use Silex\ControllerCollection;
use Silex\ControllerProviderInterface;

class SetupControllerProvider implements ControllerProviderInterface
{
    /**
     * @inheritdoc
     */
    public function connect(Application $app)
    {
        /** @var ControllerCollection $controllers */
        $controllers = $app['controllers_factory'];

        $controllers->get('/', 'controller.setup:startAction')->bind('setup');
        $controllers->post('/', 'controller.setup:action')->bind('setup_execute');

        return $controllers;
    }
}
