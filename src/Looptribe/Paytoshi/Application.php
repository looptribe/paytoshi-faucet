<?php

namespace Looptribe\Paytoshi;

use Looptribe\Paytoshi\Controller;
use Looptribe\Paytoshi\Model\Configurator;
use Looptribe\Paytoshi\Model\SettingsRepository;
use Looptribe\Paytoshi\Model\SetupDiagnostics;
use Looptribe\Paytoshi\Security\AlphaNumericPasswordGenerator;
use Looptribe\Paytoshi\Security\BCryptSaltGenerator;
use Looptribe\Paytoshi\Security\CryptPasswordEncoder;
use Looptribe\Paytoshi\Templating\LocalThemeProvider;
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

        $app['rootPath'] = realpath(__DIR__ . '/../../..');

        $app->register(new Provider\ServiceControllerServiceProvider());
        $app['themes.default'] = 'default';
        $app['themes.path'] = $app['rootPath'] . '/themes';
        $app->register(new Provider\TwigServiceProvider(), array(
            'twig.path' => array($app['rootPath'] . '/src/Looptribe/Paytoshi/Resources/views', $app['themes.path'])
        ));
        $app->register(new Provider\UrlGeneratorServiceProvider());
        $app->register(new Provider\DoctrineServiceProvider());
        $app->register(new Provider\SessionServiceProvider());
        $app->register(new Provider\SecurityServiceProvider());

        $app['config'] = $app->share(function () use ($app) {
            return Application::loadConfig($app['rootPath'] . '/config/config.yml');
        });

        $app['security.firewalls'] = $app->share(function () use ($app) {
            $adminPassword = null;
            if (!$app['setup.diagnostics']->requiresSetup()) {
                $adminPassword = $app['repository.settings']->get('password');
            }
            return array(
                'admin' => array(
                    'pattern' => '^/admin',
                    'form' => array('login_path' => '/login', 'check_path' => '/admin/login_check'),
                    'logout' => array('logout_path' => '/admin/logout', 'invalidate_session' => true),
                    'users' => array(
                        'admin' => array('ROLE_ADMIN', $adminPassword),
                    ),
                ),
            );
        });

        $app['security.encoder.digest'] = $app->share(function () use ($app) {
            return new CryptPasswordEncoder();
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
        $app['themeProvider'] = $app->share(function() use ($app) {
            return new LocalThemeProvider($app['repository.settings'], $app['themes.path'], $app['themes.default']);
        });

        $app['repository.settings'] = $app->share(function () use ($app) {
            return new SettingsRepository($app['db']);
        });

        $app['controller.index'] = $app->share(function () use ($app) {
            return new Controller\IndexController($app['templating'], $app['themeProvider'], $app['repository.settings']);
        });
        $app['controller.faq'] = $app->share(function () use ($app) {
            return new Controller\FaqController($app['templating'], $app['themeProvider'], $app['repository.settings']);
        });
        $app['controller.setup'] = $app->share(function () use ($app) {
            return new Controller\SetupController($app['templating'], $app['setup.diagnostics'], $app['setup.configurator']);
        });
        $app['controller.admin'] = $app->share(function () use ($app) {
            return new Controller\AdminController($app['templating'], $app['url_generator'], $app['repository.settings'], $app['themeProvider']);
        });

        $app['security.passwordGenerator'] = $app->share(function () use ($app) {
            return new AlphaNumericPasswordGenerator();
        });
        $app['security.saltGenerator'] = $app->share(function () use ($app) {
            return new BCryptSaltGenerator();
        });

        $app['setup.diagnostics'] = $app->share(function () use ($app) {
            return new SetupDiagnostics($app['db'], $app['repository.settings']);
        });
        $app['setup.configurator'] = $app->share(function () use ($app) {
            return new Configurator($app['db'], $app['security.passwordGenerator'], $app['security.saltGenerator'],
                $app['security.encoder.digest'], $app['rootPath'] . '/data/setup.sql');
        });

        $requireSetup = function (Request $request, Application $app) {
            if ($app['setup.diagnostics']->requiresSetup()) {
                return new RedirectResponse($app['url_generator']->generate('setup'));
            }
        };

        $app->mount('/', new Controller\PublicControllerProvider($requireSetup));
        $app->mount('/admin', new Controller\AdminControllerProvider($requireSetup));
        $app->mount('/setup', new Controller\SetupControllerProvider());

        $app->get('/login', function (Request $request) use ($app) {
            /** @var \Closure $lastError */
            $lastError = $app['security.last_error'];
            return $app['twig']->render('admin/login.html.twig', array(
                'error' => $lastError($request),
            ));
        })->bind('login');
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
