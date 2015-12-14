<?php

namespace Looptribe\Paytoshi;

use Doctrine\DBAL\Exception\TableNotFoundException;
use Looptribe\Paytoshi\Controller;
use Looptribe\Paytoshi\Model\SettingsRepository;
use Looptribe\Paytoshi\Templating\TwigTemplatingEngine;
use Silex\Provider;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Yaml\Exception\ParseException;
use Symfony\Component\Yaml\Yaml;

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

        $app['root_path'] = realpath(__DIR__ . '/../../..');

        $app->register(new Provider\ServiceControllerServiceProvider());
        $app->register(new Provider\TwigServiceProvider(), array(
            'twig.path' => $app['root_path'] . '/themes',
        ));
        $app->register(new Provider\UrlGeneratorServiceProvider());
        $app->register(new Provider\DoctrineServiceProvider());

        $app['config'] = $app->share(function () use ($app) {
            return Application::loadConfig($app['root_path'] . '/config/config.yml');
        });

        $app['db.options'] = $app->share(function () use ($app) {
            return array(
                'dbname' => $app['config']['database']['name'],
                'user' => $app['config']['database']['username'],
                'password' => $app['config']['database']['password'],
                'host' => $app['config']['database']['host'],
            );
        });

        $app['templating'] = $app->share(function () use ($app) {
            return new TwigTemplatingEngine($app['twig']);
        });

        $app['repository.settings'] = $app->share(function () use ($app) {
            return new SettingsRepository($app['db']);
        });

        $app['controller.index'] = $app->share(function () use ($app) {
            return new Controller\IndexController($app['templating']);
        });
        $app['controller.setup'] = $app->share(function () use ($app) {
            return new Controller\SetupController($app['templating']);
        });

        $requireSetup = function (Request $request, Application $app) {
            try {
                if (null === $app['repository.settings']->get('password')) {
                    return new RedirectResponse($app['url_generator']->generate('setup'));
                }
            }
            catch (TableNotFoundException $ex) {
                return new RedirectResponse($app['url_generator']->generate('setup'));
            }
        };

        $app->mount('/', new Controller\PublicControllerProvider($requireSetup));
        $app->mount('/admin', new Controller\AdminControllerProvider($requireSetup));
        $app->mount('/setup', new Controller\SetupControllerProvider());
    }

    public static function loadConfig($path)
    {
        if (false === is_readable($path)) {
            throw new ParseException(sprintf('Unable to parse "%s" as the file is not readable.', $path));
        }
        $input = file_get_contents($path);
        return Yaml::parse($input);
    }
}
