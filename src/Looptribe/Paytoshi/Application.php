<?php

namespace Looptribe\Paytoshi;

use Looptribe\Paytoshi\Api\PaytoshiApi;
use Looptribe\Paytoshi\Captcha\CaptchaProviderFactory;
use Looptribe\Paytoshi\Controller;
use Looptribe\Paytoshi\Logic\IntervalEnforcer;
use Looptribe\Paytoshi\Logic\RewardLogic;
use Looptribe\Paytoshi\Logic\RewardMapper;
use Looptribe\Paytoshi\Logic\RewardProvider;
use Looptribe\Paytoshi\Model\PayoutMapper;
use Looptribe\Paytoshi\Model\PayoutQueryBuilder;
use Looptribe\Paytoshi\Model\PayoutRepository;
use Looptribe\Paytoshi\Setup\Configurator;
use Looptribe\Paytoshi\Model\ConnectionFactory;
use Looptribe\Paytoshi\Model\SettingsRepository;
use Looptribe\Paytoshi\Setup\Diagnostics;
use Looptribe\Paytoshi\Security\AlphaNumericPasswordGenerator;
use Looptribe\Paytoshi\Security\BCryptSaltGenerator;
use Looptribe\Paytoshi\Security\CryptPasswordEncoder;
use Looptribe\Paytoshi\Templating\LocalThemeProvider;
use Looptribe\Paytoshi\Templating\TwigTemplatingEngine;
use Looptribe\Paytoshi\Twig\PaytoshiTwigExtension;
use Silex\Provider;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\Yaml\Exception\ParseException;
use Symfony\Component\Yaml\Yaml;
use Symfony\Component\Debug\ErrorHandler;
use Symfony\Component\Debug\ExceptionHandler;

class Application extends \Silex\Application
{
    // Cloudflare support
    private $trustedProxies = array(
        '103.21.244.0/22',
        '103.22.200.0/22',
        '103.31.4.0/22',
        '104.16.0.0/12',
        '108.162.192.0/18',
        '141.101.64.0/18',
        '162.158.0.0/15',
        '172.64.0.0/13',
        '173.245.48.0/20',
        '188.114.96.0/20',
        '190.93.240.0/20',
        '197.234.240.0/22',
        '198.41.128.0/17',
        '199.27.128.0/21'
    );

    public function __construct()
    {
        ErrorHandler::register();
        $handler = ExceptionHandler::register(false);
        $handler->setHandler(function (\Exception $e) {
            echo $e->getMessage();
        });
        parent::__construct();
        $this->init();
    }

    private function init()
    {
        $app = $this;

        Request::setTrustedProxies($this->trustedProxies);

        $app['rootPath'] = realpath(__DIR__ . '/../../..');
        $app['configPath'] = $app['rootPath'] . '/config/config.yml';

        $composerJson = json_decode(@file_get_contents($app['rootPath'] . '/composer.json'), true);
        $app['version'] = isset($composerJson['version']) ? $composerJson['version'] : '';

        $app['apiUrl'] = 'http://paytoshi.org/api/v1/';

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

        $app['flashbag'] = $app->share(function () use($app) {
            return $app['session']->getFlashBag();
        });

        $app['config'] = $app->share(function () use ($app) {
            return Application::loadConfig($app['configPath']);
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

        $app['connectionFactory'] = $app->share(function () {
            return new ConnectionFactory();
        });

        $app['buzz'] = $app->share(function () {
            $browser = new \Buzz\Browser();
            $browser->getClient()->setVerifyPeer(false);
            return $browser;
        });

        $app['captcha.provider'] = $app->share(function () use ($app) {
            return $app['captcha.factory']->create($app['repository.settings']->get('captcha_provider'));
        });

        $app['captcha.factory'] = $app->share(function () use($app) {
            return new CaptchaProviderFactory($app['buzz'], $app['repository.settings']);
        });

        $app['api'] = $app->share(function () use ($app) {
            return new PaytoshiApi($app['buzz'], $app['apiUrl']);
        });

        $app['twig.extension'] = $app->share(function () use ($app) {
            return new PaytoshiTwigExtension($app['request']);
        });

        $app['twig'] = $app->share($app->extend('twig', function ($twig, $app) {
            $twig->addExtension($app['twig.extension']);
            return $twig;
        }));

        $app['templating'] = $app->share(function () use ($app) {
            return new TwigTemplatingEngine($app['twig']);
        });
        $app['themeProvider'] = $app->share(function() use ($app) {
            return new LocalThemeProvider($app['repository.settings'], $app['themes.path'], $app['themes.default']);
        });

        $app['mapper.payout'] = $app->share(function () use ($app) {
            return new PayoutMapper();
        });

        $app['mapper.reward'] = $app->share(function () use ($app) {
            return new RewardMapper();
        });

        $app['querybuilder.payout'] = $app->share(function () use ($app) {
            return new PayoutQueryBuilder($app['db']);
        });

        $app['repository.payout'] = $app->share(function () use ($app) {
            return new PayoutRepository($app['db'], $app['mapper.payout'], $app['querybuilder.payout']);
        });

        $app['repository.settings'] = $app->share(function () use ($app) {
            return new SettingsRepository($app['db']);
        });

        $app['controller.index'] = $app->share(function () use ($app) {
            return new Controller\IndexController($app['templating'], $app['themeProvider'], $app['repository.settings'], $app['flashbag'], $app['captcha.provider'], $app['logic.reward_provider']);
        });
        $app['controller.reward'] = $app->share(function () use ($app) {
            return new Controller\RewardController($app['templating'], $app['themeProvider'], $app['repository.settings'], $app['captcha.provider'], $app['url_generator'], $app['logic.reward'], $app['flashbag']);
        });
        $app['controller.faq'] = $app->share(function () use ($app) {
            return new Controller\FaqController($app['templating'], $app['themeProvider'], $app['repository.settings']);
        });
        $app['controller.setup'] = $app->share(function () use ($app) {
            return new Controller\SetupController($app['templating'], $app['url_generator'], $app['setup.diagnostics'], $app['setup.configurator'], $app['db.options']);
        });
        $app['controller.admin'] = $app->share(function () use ($app) {
            return new Controller\AdminController($app['templating'], $app['url_generator'], $app['repository.settings'], $app['themeProvider'], $app['api'], $app['mapper.reward'], $app['version']);
        });

        $app['logic.reward'] = $app->share(function () use ($app) {
            return new RewardLogic($app['db'], $app['repository.payout'], $app['logic.reward_provider'], $app['api'], $app['logic.interval_enforcer'], $app['captcha.provider'], $app['repository.settings']->get('api_key'), $app['repository.settings']->get('referral_percentage'));
        });

        $app['logic.reward_provider'] = $app->share(function () use ($app) {
            return new RewardProvider($app['mapper.reward'], $app['repository.settings']->get('rewards'));
        });

        $app['logic.interval_enforcer'] = $app->share(function () use ($app) {
            return new IntervalEnforcer($app['repository.payout'], $app['repository.settings']->get('waiting_interval'));
        });

        $app['security.passwordGenerator'] = $app->share(function () use ($app) {
            return new AlphaNumericPasswordGenerator();
        });
        $app['security.saltGenerator'] = $app->share(function () use ($app) {
            return new BCryptSaltGenerator();
        });

        $app['setup.diagnostics'] = $app->share(function () use ($app) {
            return new Diagnostics($app['repository.settings'], $app['connectionFactory'], $app['configPath']);
        });
        $app['setup.configurator'] = $app->share(function () use ($app) {
            return new Configurator($app['db'], $app['security.passwordGenerator'], $app['security.saltGenerator'],
                $app['security.encoder.digest'], $app['rootPath'] . '/data/setup.sql', $app['configPath']);
        });

        $requireSetup = function (Request $request, Application $app) {
            if ($app['setup.diagnostics']->requiresSetup()) {
                //return new RedirectResponse('index.php/' . $app['url_generator']->generate('setup', array(), UrlGenerator::RELATIVE_PATH));
                return $app['controller.setup']->startAction();
            }
        };

        $setupAlreadyDone = function (Request $request, Application $app) {
            if (!$app['setup.diagnostics']->requiresSetup()) {
                throw new AccessDeniedHttpException('Setup already completed.');
            }
        };

        $app->mount('/', new Controller\PublicControllerProvider($requireSetup));
        $app->mount('/admin', new Controller\AdminControllerProvider($requireSetup));
        $app->mount('/setup', new Controller\SetupControllerProvider($setupAlreadyDone));

        $app->get('/login', function (Request $request) use ($app) {
            /** @var \Closure $lastError */
            $lastError = $app['security.last_error'];
            return $app['templating']->render('admin/login.html.twig', array(
                'error' => $lastError($request),
            ));
        })->bind('login');

        $app->before(function (Request $request) {
            if (0 === strpos($request->headers->get('Content-Type'), 'application/json')) {
                $data = json_decode($request->getContent(), true);
                $request->request->replace(is_array($data) ? $data : array());
            }
        });
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
