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
use Looptribe\Paytoshi\Model\SettingRepository;
use Looptribe\Paytoshi\Service\ApiService;
use Looptribe\Paytoshi\Service\Captcha\CaptchaServiceFactory;
use Looptribe\Paytoshi\Service\Captcha\RecaptchaService;
use Looptribe\Paytoshi\Service\Captcha\SolveMediaService;
use Looptribe\Paytoshi\Service\DatabaseService;
use Looptribe\Paytoshi\Service\RewardService;
use Looptribe\Paytoshi\Service\ThemeService;
use Slim\Middleware\SessionCookie;
use Slim\Slim;
use Slim\Views\TwigExtension;

class App extends Slim {
    private $defaultSettings = array(
        'config_file' => 'config/config.yml',
        'templates.path' => 'themes',
        'setup' => 'data/setup.sql',
        'version' => 1,
        'debug' => false,
        'view' => '\Slim\Views\Twig',
        'api_url' => 'http://pipe.paytoshi.org:3001/v1/faucet/send',
        'balance_url' => 'https://paytoshi.org/_ADDRESS_/balance',
        'default_theme' => 'default'
    );
    
    public function __construct(array $userSettings = array()) {
        $this->defaultSettings['cookies.encrypt'] = extension_loaded('mcrypt');
        $settings = array_unique(array_merge($this->defaultSettings, $userSettings));
        parent::__construct($settings);
        
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
            return $this->render($this->ThemeService->getTemplate('error.html.twig'), array('message' => $e->getMessage()));
        });
    }
    
    private function registerHooks() {
        // HOOKS
        $this->hook('slim.before.dispatch', function () {
            if ((!$this->SettingRepository || $this->SettingRepository->isNew()) && $this->router()->getCurrentRoute()->getName() != 'setup') {
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

        $this->container->singleton('SettingRepository', function () {
            try {
                return new SettingRepository($this->DatabaseService);
            }
            catch(PaytoshiException $e) {
                return null;
            }
        });
        
        $this->container->singleton('ApiService', function () {
            return new ApiService(array(
                'settingRepository'    => $this->SettingRepository, 
                'config'                => array('api_url' => $this->config('api_url'),
                )
            ));
        });
        
        $this->container->singleton('CaptchaServiceFactory', function() {
            return new CaptchaServiceFactory($this);
        });
        
        $this->container->singleton('SolveMediaService', function() {
            return new SolveMediaService($this, $this->SettingRepository);
        });
        
        $this->container->singleton('RecaptchaService', function() {
            return new RecaptchaService($this, $this->SettingRepository);
        });
        
        $this->container->singleton('RecipientRepository', function() {
            return new RecipientRepository($this->DatabaseService);
        });
        
        $this->container->singleton('PayoutRepository', function() {
            return new PayoutRepository($this->DatabaseService);
        });
        
        $this->container->singleton('RewardService', function () {
            return new RewardService($this->SettingRepository->getRewards());
        });
        
        $this->container->singleton('ThemeService', function () {
            return new ThemeService(array(
                'settingRepository'    => $this->SettingRepository, 
                'config'                => array(
                    'default_theme' => $this->config('default_theme'), 
                    'template_path'  => $this->config('templates.path'))
            ));
        });
    }
    
    private function registerControllers() {
        // CONTROLLERS
        $this->container->singleton('AdminController', function () {
            return new AdminController($this, array(
                'databaseService'       =>    $this->DatabaseService, 
                'settingRepository'     =>    $this->SettingRepository,
                'themeService'          =>    $this->ThemeService,
                'config'                =>    array('setup' => $this->config('setup'))
            ));
        });
        
        $this->container->singleton('DefaultController', function () {
            return new DefaultController($this, array( 
                'databaseService'       =>    $this->DatabaseService, 
                'settingRepository'     =>    $this->SettingRepository, 
                'captchaServiceFactory' =>    $this->CaptchaServiceFactory,
                'recipientRepository'   =>    $this->RecipientRepository,
                'payoutRepository'      =>    $this->PayoutRepository,
                'apiService'            =>    $this->ApiService,
                'rewardService'         =>    $this->RewardService,
                'themeService'          =>    $this->ThemeService
            ));
        });
    }
    
    private function registerRoutes() {
        // ROUTES
        $this->get('/setup', function () {
            if (!$this->SettingRepository || $this->SettingRepository->isNew())
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
        
        $this->get('/faq', function () {
            $this->DefaultController->faq();
        })->name('faq');

        $this->get('/', function () {
            if ($this->SettingRepository->isIncomplete())
                return $this->DefaultController->incomplete();
                
            $this->DefaultController->index();
        })->name('index');
    }
}
