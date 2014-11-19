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

namespace Looptribe\Paytoshi\Controller;

use Exception;
use Looptribe\Paytoshi\Exception\PaytoshiException;

class AdminController {
    protected $app;
    protected $config;
    protected $database;
    protected $settingRepository;
    protected $themeService;
    
    public function __construct($app, $options) {
        $this->app = $app;
        $this->database = $options['databaseService'];
        $this->settingRepository = $options['settingRepository'];
        $this->themeService = $options['themeService'];
        $this->config = $options['config'];
    }
    
    public function setup() {
        $password = $this->generateRandomString(16);
        $hash = crypt($password);
        $this->setupDatabase(array(
            'password' => $hash,
            'cookie_secret_key' => $this->generateRandomString(16),
            'theme' => $this->app->config('default_theme')
        ));
        $this->app->render($this->themeService->getTemplate('setup.html.twig'), array(
            'password' => $password,
        ));
    }
    
    public function login() {
        if ($this->app->getCookie('authenticated'))
            $this->app->response->redirect($this->app->urlFor('admin'));
            
        if ($this->app->request->isGet())
            $this->app->render($this->themeService->getTemplate('login.html.twig'), array(
                'name' => $this->settingRepository->getName()
            ));
        else if ($this->app->request->isPost()) {
            $password = $this->app->request->post('password');
            if (crypt($password, $this->settingRepository->getPassword()) === $this->settingRepository->getPassword()) {
                if ($this->app->config('cookie.encrypt'))
                    $this->app->config('cookie.secret_key', $this->settingRepository->getCookieSecretKey());
                
                $this->app->setCookie('authenticated', true);
                $this->app->response->redirect($this->app->urlFor('admin'));
            }
            else {
                $this->app->flashNow('login_error', 'Incorrect Password.');
                $this->app->render($this->themeService->getTemplate('login.html.twig'), array(
                    'name' => $this->settingRepository->getName()
                ), 403);
            }
        }
    }
    
    public function admin() {
        if ($this->app->config('cookie.encrypt'))
            $this->app->config('cookie.secret_key', $this->settingRepository->getCookieSecretKey());
            
        $isAuthenticated = $this->app->getCookie('authenticated');
        if (!$isAuthenticated)
            return $this->app->redirect($this->app->urlFor('login'));
        
        if ($this->app->request->isGet()) {
            $params = array_merge($this->settingRepository->getAdminView(), array('themes' => $this->themeService->getThemes()));
            return $this->app->render($this->themeService->getTemplate('admin.html.twig'), $params);
        }
        else if ($this->app->request->isPost()) {
            $data = array();
            parse_str($this->app->request->getBody(), $data);
            try {
                $this->settingRepository->save($data);
                $this->app->flash('save_success', 'Settings saved successfully.');
            }
            catch (PaytoshiException $e) {
                $this->app->flash('save_error', 'Cannot save settings.');
            }
            return $this->app->response->redirect($this->app->urlFor('admin'));
        }
    }
    
    private function setupDatabase($params) {
        try {
            $sql = file_get_contents($this->config['setup']);
        }
        catch (Exception $e)  {
            throw new PaytoshiException(sprintf('Unable to find database sql script. Please check that %s exists.', $this->config['setup']), null, $e);
        }
        $this->database->run($sql, $params);
    }
    
    private function generateRandomString($length) {
        $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $randomString = '';
        for ($i = 0; $i < $length; $i++) {
            $randomString .= $characters[rand(0, strlen($characters) - 1)];
        }
        return $randomString;
    }
    
}