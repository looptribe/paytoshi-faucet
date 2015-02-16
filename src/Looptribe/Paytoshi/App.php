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
use Slim\Slim;
use Slim\Views\TwigExtension;

class App extends Slim {
    private $defaultSettings = array(
        'config_file' => 'config/config.yml',
        'templates.path' => 'themes',
        'setup' => 'data/setup.sql',
        'version' => 1,
        'debug' => true,
        'view' => '\Slim\Views\Twig',
        'api_url' => 'http://sato.local/app_dev.php/api/v1/faucet/send',
        'balance_url' => 'http://sato.local/app_dev.php/_ADDRESS_/balance',
        'default_theme' => 'default'
    );
    
    public function __construct(array $userSettings = array()) {
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
        
        $this->registerErrorHandler();
        $this->registerHooks();
        $this->registerServices();
        $this->registerControllers();
        $this->registerRoutes();
    }
    
    private function registerErrorHandler() {
        // ERROR HANDLER
        $self = $this;
        $this->error(function(Exception $e) use($self) {
            return $self->render($self->ThemeService->getTemplate('error.html.twig'), array('message' => $e->getMessage()));
        });
    }
    
    private function registerHooks() {
        // HOOKS
        $self = $this;
        $this->hook('slim.before.dispatch', function() use($self) {
            if ((!$self->SettingRepository || $self->SettingRepository->isNew()) && $self->router()->getCurrentRoute()->getName() != 'setup') {
                $self->redirect($self->urlFor('setup'));
            }
        });
    }
    
    private function registerServices() {
        // SERVICES
        $self = $this;
        $this->container->singleton('DatabaseService', function() use($self) {
            return new DatabaseService(array(
                'config_file' => $self->config('config_file')
            ));
        });

        $this->container->singleton('SettingRepository', function() use($self) {
            try {
                return new SettingRepository($self->DatabaseService);
            }
            catch(PaytoshiException $e) {
                return null;
            }
        });
        
        $this->container->singleton('ApiService', function() use($self) {
            return new ApiService(array(
                'settingRepository'    => $self->SettingRepository, 
                'config'                => array('api_url' => $self->config('api_url'),
                )
            ));
        });
        
        $this->container->singleton('CaptchaServiceFactory', function() use($self) {
            return new CaptchaServiceFactory($self);
        });
        
        $this->container->singleton('SolveMediaService', function() use($self) {
            return new SolveMediaService($self, $self->SettingRepository);
        });
        
        $this->container->singleton('RecaptchaService', function() use($self) {
            return new RecaptchaService($self, $self->SettingRepository);
        });
        
        $this->container->singleton('RecipientRepository', function() use($self) {
            return new RecipientRepository($self->DatabaseService);
        });
        
        $this->container->singleton('PayoutRepository', function() use($self) {
            return new PayoutRepository($self->DatabaseService);
        });
        
        $this->container->singleton('RewardService', function() use($self) {
            return new RewardService($self->SettingRepository->getRewards());
        });
        
        $this->container->singleton('ThemeService', function() use($self) {
            return new ThemeService(array(
                'settingRepository'    => $self->SettingRepository, 
                'config'                => array(
                    'default_theme' => $self->config('default_theme'), 
                    'template_path'  => $self->config('templates.path'))
            ));
        });
    }
    
    private function registerControllers() {
        // CONTROLLERS
        $self = $this;
        $this->container->singleton('AdminController', function() use($self) {
            return new AdminController($self, array(
                'databaseService'       =>    $self->DatabaseService, 
                'settingRepository'     =>    $self->SettingRepository,
                'themeService'          =>    $self->ThemeService,
                'config'                =>    array('setup' => $self->config('setup'))
            ));
        });
        
        $this->container->singleton('DefaultController', function() use($self) {
            return new DefaultController($self, array( 
                'databaseService'       =>    $self->DatabaseService, 
                'settingRepository'     =>    $self->SettingRepository, 
                'captchaServiceFactory' =>    $self->CaptchaServiceFactory,
                'recipientRepository'   =>    $self->RecipientRepository,
                'payoutRepository'      =>    $self->PayoutRepository,
                'apiService'            =>    $self->ApiService,
                'rewardService'         =>    $self->RewardService,
                'themeService'          =>    $self->ThemeService
            ));
        });
    }
    
    private function registerRoutes() {
        // ROUTES
        $self = $this;
        $this->get('/setup', function() use($self) {
            if (!$self->SettingRepository || $self->SettingRepository->isNew())
                return $self->AdminController->setup();
            
            $self->response->redirect($self->urlFor('login'));
            
        })->name('setup');

        $this->get('/login', function() use($self) {
            $self->AdminController->login();
        })->name('login');

        $this->post('/login', function() use($self) {
            $self->AdminController->login();
        });
        
        $this->get('/logout', function() use($self) {
            $self->AdminController->logout();
        });

        $this->get('/admin', function() use($self) {
            $self->AdminController->admin();
        })->name('admin');
        
        $this->post('/admin', function() use($self) {
            $self->AdminController->admin();
        })->name('admin_post');
        
        $this->post('/reward', function() use($self) {
            $self->DefaultController->reward();
        })->name('reward');
        
        $this->get('/faq', function() use($self) {
            $self->DefaultController->faq();
        })->name('faq');

        $this->get('/', function() use($self) {
            if ($self->SettingRepository->isIncomplete())
                return $self->DefaultController->incomplete();
                
            $self->DefaultController->index();
        })->name('index');
    }
}
