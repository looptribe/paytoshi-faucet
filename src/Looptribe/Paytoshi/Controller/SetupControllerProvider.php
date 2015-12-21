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
        $controllers->post('/', 'controller.setup:saveAction')->bind('setup_save');
        $controllers->get('/complete', 'controller.setup:completeAction')->bind('setup_complete');
        $controllers->post('/check.json', 'controller.setup:checkAction')->bind('setup_check');

        return $controllers;
    }
}
