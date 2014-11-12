<?php

/**
 * Paytoshi Faucet Script
 * 
 * Contact: info@paytoshi.org
 * 
 * @author: Looptribe
 * @link: https://paytoshi.org
 * @package: Looptribe\Paytoshi 
 */

namespace Looptribe\Paytoshi;

use Exception;
use Looptribe\Paytoshi\Controller\AdminController;
use Looptribe\Paytoshi\Controller\DefaultController;
use Looptribe\Paytoshi\Exception\PaytoshiException;
use Looptribe\Paytoshi\Model\PayoutRepository;
use Looptribe\Paytoshi\Model\RecipientRepository;
use Looptribe\Paytoshi\Service\ApiService;
use Looptribe\Paytoshi\Service\Captcha\CaptchaServiceFactory;
use Looptribe\Paytoshi\Service\Captcha\SolveMediaService;
use Looptribe\Paytoshi\Service\DatabaseService;
use Looptribe\Paytoshi\Service\FaucetService;
use Looptribe\Paytoshi\Service\RewardService;
use Slim\Middleware\SessionCookie;
use Slim\Slim;
use Slim\Views\TwigExtension;

class App extends Slim {
    private $defaultSettings = array(
        'config_file' => 'config/config.yml',
        'templates.path' => 'src/Looptribe/Paytoshi/Views',
        'setup' => 'data/setup.sql',
        'version' => 1,
        'debug' => true,
        'view' => '\Slim\Views\Twig',
        'api_url' => 'http://localhost:3000/v1/private/send',
        'balance_url' => 'http://sato.local/app_dev.php/balance'
    );
    
    public function __construct(array $userSettings = array()) {
        $this->defaultSettings['cookies.encrypt'] = extension_loaded('mcrypt');
        $settings = array_unique(array_merge($this->defaultSettings, $userSettings));
        parent::__construct($settings);
        
        $this->view()->parserOptions = array(
            'charset' => 'utf-8',
            'debug' => $this->config('debug'),
            'strict_variables' => false,
            'autoescape' => true,
            'cache' => './cache'
        );
        
        $this->view()->parserExtensions = array(
            new TwigExtension(),
        );
        
        $this->add(new SessionCookie(array(
            'name' => 'paytoshi_session',
            'cipher' => MCRYPT_RIJNDAEL_256,
            'cipher_mode' => MCRYPT_MODE_CBC
        )));
        
        $this->registerErrorHandler();
        $this->registerHooks();
        $this->registerServices();
        $this->registerControllers();
        $this->registerRoutes();
    }
    
    private function registerErrorHandler() {
        // ERROR HANDLER
        $this->error(function (Exception $e) {
            return $this->render('Admin/error.html.twig', array('message' => $e->getMessage()));
        });
    }
    
    private function registerHooks() {
        // HOOKS
        $this->hook('slim.before.dispatch', function () {
            if ((!$this->FaucetService || $this->FaucetService->isNew()) && $this->router()->getCurrentRoute()->getName() != 'setup') {
                $this->redirect($this->urlFor('setup'));
            }
        });
    }
    
    private function registerServices() {
        // SERVICES
        $this->container->singleton('DatabaseService', function () {
            return new DatabaseService(array(
                'config_file' => $this->config('config_file')
            ));
        });

        $this->container->singleton('FaucetService', function () {
            try {
                return new FaucetService($this->DatabaseService);
            }
            catch(PaytoshiException $e) {
                return null;
            }
        });
        
        $this->container->singleton('ApiService', function () {
            return new ApiService($this, $this->FaucetService);
        });
        
        $this->container->singleton('CaptchaServiceFactory', function() {
            return new CaptchaServiceFactory($this);
        });
        
        $this->container->singleton('SolveMediaService', function() {
            return new SolveMediaService($this, $this->FaucetService);
        });
        
//        $this->container->singleton('RecaptchaService', function() {
//            return new RecaptchaService($this, $this->FaucetService);
//        });
        
        $this->container->singleton('RecipientRepository', function() {
            return new RecipientRepository($this->DatabaseService);
        });
        
        $this->container->singleton('PayoutRepository', function() {
            return new PayoutRepository($this->DatabaseService);
        });
        
        $this->container->singleton('RewardService', function () {
            return new RewardService($this->FaucetService->getRewards());
        });
    }
    
    private function registerControllers() {
        // CONTROLLERS
        $this->container->singleton('AdminController', function () {
            return new AdminController($this, $this->DatabaseService, $this->FaucetService, array(
                'setup' => $this->config('setup'),
            ));
        });
        
        $this->container->singleton('DefaultController', function () {
            return new DefaultController($this, 
                    $this->DatabaseService, 
                    $this->FaucetService, 
                    $this->CaptchaServiceFactory,
                    $this->RecipientRepository,
                    $this->PayoutRepository,
                    $this->ApiService,
                    $this->RewardService
            );
        });
    }
    
    private function registerRoutes() {
        // ROUTES
        $this->get('/setup', function () {
            if (!$this->FaucetService || $this->FaucetService->isNew())
                return $this->AdminController->setup();
            
            $this->response->redirect($this->urlFor('login'));
            
        })->name('setup');

        $this->get('/login', function () {
            $this->AdminController->login();
        })->name('login');

        $this->post('/login', function () {
            $this->AdminController->login();
        });

        $this->get('/admin', function () {
            $this->AdminController->admin();
        })->name('admin');
        
        $this->post('/admin', function () {
            $this->AdminController->admin();
        })->name('admin_post');
        
        $this->post('/reward', function () {
            $this->DefaultController->reward();
        })->name('reward');

        $this->get('/', function () {
            if ($this->FaucetService->isIncomplete())
                return $this->DefaultController->incomplete();
                
            $this->DefaultController->home();
        })->name('home');
    }
}
