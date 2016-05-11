<?php

namespace Looptribe\Paytoshi\Controller;

use Silex\Application;
use Silex\ControllerCollection;
use Silex\ControllerProviderInterface;

class SetupControllerProvider implements ControllerProviderInterface
{
    private $before;

    public function __construct($before)
    {
        $this->before = $before;
    }

    /**
     * @inheritdoc
     */
    public function connect(Application $app)
    {
        /** @var ControllerCollection $controllers */
        $controllers = $app['controllers_factory'];

        $controllers->get('/', 'controller.setup:setupAction')->bind('setup');
        $controllers->post('/', 'controller.setup:saveAction')->bind('setup_save');
        $controllers->get('/complete', 'controller.setup:completeAction')->bind('setup_complete');
        $controllers->post('/check.json', 'controller.setup:checkAction')->bind('setup_check');
        $controllers->get('/rewrite.json', 'controller.setup:checkRewriteAction')->bind('setup_check_rewrite');

        $controllers->before($this->before);

        return $controllers;
    }
}
