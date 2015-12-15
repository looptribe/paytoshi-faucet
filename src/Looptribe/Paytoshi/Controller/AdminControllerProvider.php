<?php

namespace Looptribe\Paytoshi\Controller;

use Silex\Application;
use Silex\ControllerCollection;
use Silex\ControllerProviderInterface;

class AdminControllerProvider implements ControllerProviderInterface
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

        $controllers->get('/', 'controller.admin:action')->bind('admin');
        $controllers->post('/', 'controller.admin:saveAction')->bind('admin_save');

        $controllers->before($this->before);

        return $controllers;
    }
}
