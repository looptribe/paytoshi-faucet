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

use Looptribe\Paytoshi\App;
use Looptribe\Paytoshi\Exception\PaytoshiException;
use Looptribe\Paytoshi\Model\SettingRepository;
use Looptribe\Paytoshi\Service\DatabaseService;
use Looptribe\Paytoshi\Service\ThemeService;

class AdminController
{
    /** @var App */
    protected $app;
    protected $config;
    /** @var  DatabaseService */
    protected $database;
    /** @var  SettingRepository */
    protected $settingRepository;
    /** @var ThemeService */
    protected $themeService;

    public function __construct(App $app, $options)
    {
        $this->app = $app;
        $this->database = $options['databaseService'];
        $this->settingRepository = $options['settingRepository'];
        $this->themeService = $options['themeService'];
        $this->config = $options['config'];
    }

    public function setup()
    {
        $password = $this->generateRandomString(16);
        $salt = '$2a$13$' . substr(strtr(base64_encode(mcrypt_create_iv(22)), '+', '.'), 0, 22);
        $hash = crypt($password, $salt);
        $this->setupDatabase(array(
            'password' => $hash,
            'theme' => $this->app->config('default_theme')
        ));
        $this->app->render($this->themeService->getTemplate('setup.html.twig'), array(
            'password' => $password,
        ));
    }

    public function login()
    {
        if (isset($_SESSION['authenticated']) && $_SESSION['authenticated']) {
            $this->app->response->redirect($this->app->urlFor('admin'));
        }

        if ($this->app->request->isGet()) {
            $this->app->render($this->themeService->getTemplate('login.html.twig'), array(
                'name' => $this->settingRepository->getName()
            ));
        } else {
            if ($this->app->request->isPost()) {
                $password = $this->app->request->post('password');
                if (crypt($password,
                        $this->settingRepository->getPassword()) === $this->settingRepository->getPassword()
                ) {
                    $_SESSION['authenticated'] = true;
                    $this->app->response->redirect($this->app->urlFor('admin'));
                } else {
                    $this->app->flashNow('login_error', 'Incorrect Password.');
                    $this->app->render($this->themeService->getTemplate('login.html.twig'), array(
                        'name' => $this->settingRepository->getName()
                    ), 403);
                }
            }
        }
    }

    public function logout()
    {
        unset($_SESSION['authenticated']);
        $this->app->redirect($this->app->urlFor('login'));
    }

    public function admin()
    {
        if (!isset($_SESSION['authenticated']) || !$_SESSION['authenticated']) {
            $this->app->redirect($this->app->urlFor('login'));
            return;
        }

        if ($this->app->request->isGet()) {
            $params = array_merge($this->getView(),
                array('themes' => $this->themeService->getThemes()));
            $this->app->render($this->themeService->getTemplate('admin.html.twig'), $params);
            return;
        } else {
            if ($this->app->request->isPost()) {
                $data = array();
                parse_str($this->app->request->getBody(), $data);
                try {
                    $this->settingRepository->save($data);
                    $this->app->flash('save_success', 'Settings saved successfully.');
                } catch (PaytoshiException $e) {
                    $this->app->flash('save_error', 'Cannot save settings.');
                }
                $this->app->response->redirect($this->app->urlFor('admin'));
                return;
            }
        }
    }

    private function setupDatabase($params)
    {
        $sql = file_get_contents($this->config['setup']);
        if (!$sql) {
            throw new PaytoshiException(sprintf('Unable to find database sql script. Please check that %s exists.',
                $this->config['setup']), null);
        }
        $this->database->run($sql, $params);
    }

    private function generateRandomString($length)
    {
        $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $randomString = '';
        for ($i = 0; $i < $length; $i++) {
            $randomString .= $characters[rand(0, strlen($characters) - 1)];
        }
        return $randomString;
    }

    private function getView()
    {
        return array(
            'version' => $this->settingRepository->getVersion(),
            'api_key' => $this->settingRepository->getApiKey(),
            'name' => $this->settingRepository->getName(),
            'description' => $this->settingRepository->getDescription(),
            'current_theme' => $this->settingRepository->getTheme(),
            'captcha_provider' => $this->settingRepository->getCaptchaProvider(),
            'solve_media' => array(
                'challenge_key' => $this->settingRepository->getSolveMediaChallengeKey(),
                'verification_key' => $this->settingRepository->getSolveMediaVerificationKey(),
                'authentication_key' => $this->settingRepository->getSolveMediaAuthenticationKey(),
            ),
            'recaptcha' => array(
                'public_key' => $this->settingRepository->getRecaptchaPublicKey(),
                'private_key' => $this->settingRepository->getRecaptchaPrivateKey()
            ),
            'funcaptcha' => array(
                'public_key' => $this->settingRepository->getFuncaptchaPublicKey(),
                'private_key' => $this->settingRepository->getFuncaptchaPrivateKey()
            ),
            'waiting_interval' => $this->settingRepository->getWaitingInterval(),
            'rewards' => $this->settingRepository->getRewards(),
            'referral_percentage' => $this->settingRepository->getReferralPercentage(),
            'css' => $this->settingRepository->getCss(),
            'header_box' => $this->settingRepository->getHeaderBox(),
            'left_box' => $this->settingRepository->getLeftBox(),
            'right_box' => $this->settingRepository->getRightBox(),
            'center1_box' => $this->settingRepository->getCenter1Box(),
            'center2_box' => $this->settingRepository->getCenter2Box(),
            'center3_box' => $this->settingRepository->getCenter3Box(),
            'footer_box' => $this->settingRepository->getFooterBox(),
        );
    }
}
