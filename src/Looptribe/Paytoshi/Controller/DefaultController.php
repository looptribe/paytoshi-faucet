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

use DateInterval;
use DateTime;
use Looptribe\Paytoshi\Exception\PaytoshiException;
use Looptribe\Paytoshi\Model\Payout;
use Looptribe\Paytoshi\Model\Recipient;
use Looptribe\Paytoshi\Model\SettingRepository;
use Looptribe\Paytoshi\Service\ApiResponse;
use Looptribe\Paytoshi\Service\Captcha\CaptchaException;
use Looptribe\Paytoshi\Service\DatabaseService;

class DefaultController {

    protected $app;
    /* @var $database DatabaseService */
    protected $database;
    /* @var $settingRepository SettingRepository */
    protected $settingRepository;
    protected $captchaService;
    protected $recipientRepository;
    protected $payoutRepository;
    protected $apiService;
    protected $rewardService;
    protected $themeService;

    public function __construct($app, $options = array()) {
        $this->app = $app;
        $this->database = $options['databaseService'];
        $this->settingRepository = $options['settingRepository'];
        $this->captchaService = $options['captchaServiceFactory']->getService($this->settingRepository->getCaptchaProvider());
        $this->recipientRepository = $options['recipientRepository'];
        $this->payoutRepository = $options['payoutRepository'];
        $this->apiService = $options['apiService'];
        $this->rewardService = $options['rewardService'];
        $this->themeService = $options['themeService'];
        
    }
    
    public function incomplete() {
        return $this->app->render($this->themeService->getTemplate('incomplete.html.twig'), array(
            'name' => $this->settingRepository->getName()
        ));
    }

    public function index() {
        
        return $this->app->render($this->themeService->getTemplate('index.html.twig'), array(
            'name' => $this->settingRepository->getName(),
            'description' => $this->settingRepository->getDescription(),
            'referral' => $this->app->request->get('r'),
            'referral_percentage' => $this->settingRepository->getReferralPercentage(),
            'rewards' => $this->rewardService->getAsArray(),
            'rewards_average' => $this->rewardService->getAverage(),
            'waiting_interval' => $this->settingRepository->getWaitingInterval(),
            'address' => $this->app->getCookie('address'),
            'base_url' => $this->app->request->getUrl(),
            'captcha' => array(
                'name' => $this->captchaService->getName(),
                'server' => $this->captchaService->getServer(),
                'public_key' => $this->captchaService->getPublicKey()
            ),
            'content' => array(
                'header_box' => $this->settingRepository->getHeaderBox(),
                'left_box' => $this->settingRepository->getLeftBox(),
                'right_box' => $this->settingRepository->getRightBox(),
                'center1_box' => $this->settingRepository->getCenter1Box(),
                'center2_box' => $this->settingRepository->getCenter2Box(),
                'center3_box' => $this->settingRepository->getCenter3Box(),
                'footer_box' => $this->settingRepository->getFooterBox()
            ),
            'theme' => array(
                'name' => $this->settingRepository->getTheme(),
                'css' => $this->settingRepository->getCss()
            )
        ));
    }

    /**
     * 1. Captcha check
     * 2. Timeout check
     * 3. Reward generation
     * 4. Payout creation
     * 5. Payment process
     * 6. Referral payout creation
     * 7. Referral payment process
     */
    public function reward() {
        $address = $this->app->request->post('address');
        $challenge = $this->app->request->post($this->captchaService->getChallengeName());
        $response = $this->app->request->post($this->captchaService->getResponseName());
        if (empty($address) || empty($challenge) || empty($response)) {
            $this->app->flash('warning', 'Missing address or captcha.');
            $this->app->redirect($this->app->urlFor('index'));
        }
        
        $remoteIp = $this->app->request->getIp();

        // Captcha Check
        try {
            $captchaResponse = $this->captchaService->checkAnswer($remoteIp, $challenge, $response);
        }
        catch (CaptchaException $e) {
            $this->app->flash('error', 'Unable to complete request.');
            return $this->app->redirect($this->app->urlFor('index'));
        }
        
        if (!$captchaResponse->getSuccess()) {
            $this->app->flash('error', 'Invalid Captcha');
            return $this->app->redirect($this->app->urlFor('index'));
        }
        
        try {
            if (!$this->database->beginTransaction());
        }
        catch(PaytoshiException $e) {
            $this->app->flash('error', 'Unable to complete request.');
            $this->app->redirect($this->app->urlFor('index'));
        }
        
        $recipient = $this->recipientRepository->findOneByAddress($address);
        if (!$recipient) {
            $recipient = new Recipient();
            $recipient->setAddress($address);
        }
        
        // Timeout check
        $lastPayout = $this->payoutRepository->findLastByRecipientAndIp($recipient, $remoteIp);
        $now = new DateTime;
        $waitingInterval = $this->settingRepository->getWaitingInterval();
        if ($lastPayout) {
            $nextPayoutTime = $lastPayout->getCreatedAt()->add(new DateInterval('PT' . $waitingInterval . 'S'));
            if ($nextPayoutTime > $now)
            {
                $this->database->rollBack();
                $waitingTime = $nextPayoutTime->diff($now);
                $this->app->flash('warning', sprintf('You can get a reward again in %s.', $this->formatTime($waitingTime)));
                return $this->app->redirect($this->app->urlFor('index'));
            }
        }
        
        // Reward Generation
        $earning = $this->rewardService->getReward();
        
        // Payout Creation
        $payout = new Payout();
        $payout->setIp($remoteIp);
        $payout->setRecipientAddress($recipient->getAddress());
        $payout->setEarning($earning);
        
        // Payment process
        try {
            /* @var $apiResponse ApiResponse */
            $apiResponse = $this->apiService->send($payout->getRecipientAddress(), $payout->getEarning());
        }
        catch(PaytoshiException $e) {
            $this->database->rollback();
            $this->app->flash('error', $e->getMessage());
            return $this->app->redirect($this->app->urlFor('index'));
        }
        
        if (!$apiResponse->getSuccess()) {
            $this->database->rollback();
            $this->app->flash('error', $apiResponse->getError());
            return $this->app->redirect($this->app->urlFor('index'));
        }
        
        $view = $this->app->view();
        $view->setData(array(
            'amount' => $apiResponse->getAmount(),
            'recipient' => $apiResponse->getRecipient(),
            'balanceUrl' => $this->app->config('balance_url')
        ));
        
        $this->recipientRepository->save($recipient);
        $this->app->flash('success', $view->render($this->themeService->getTemplate('balance.html.twig')));
        $this->app->setCookie('address', $recipient->getAddress());
        
        $referral = $this->app->request->post('referral');
        $referralPercentage = $this->settingRepository->getReferralPercentage();
        if ($referral && $referral != $address && $referralPercentage > 0)
        {
            // Referral Payout Creation
            $referralRecipient = $this->recipientRepository->findOneByAddress($referral);
            if (!$referralRecipient) {
                $referralRecipient = new Recipient();
                $referralRecipient->setAddress($referral);
            }
            
            $referralEarning = ceil($earning * $referralPercentage / 100);
            $payout->setReferralRecipientAddress($referralRecipient->getAddress());
            $payout->setReferralEarning($referralEarning);
            $referralRecipient->setReferralEarning($referralEarning + $referralRecipient->getReferralEarning());

            // Referral payment process
            try {
                $apiResponse = $this->apiService->send(
                        $referralRecipient->getAddress(), 
                        $referralRecipient->getReferralEarning(),
                        'Referral Earnings'
                );
            }
            catch(PaytoshiException $e) {
                $this->payoutRepository->save($payout);
                $this->recipientRepository->save($referralRecipient);
                $this->database->commit();
                $this->app->flash('error', $e->getMessage());
                return $this->app->redirect($this->app->urlFor('index'));
            }

            if ($apiResponse->getSuccess())
                $referralRecipient->setReferralEarning(0);
            
            $this->recipientRepository->save($referralRecipient);
        }
        $this->payoutRepository->save($payout);
        $this->database->commit();
        
        return $this->app->redirect($this->app->urlFor('index'));
    }
    
    private function formatTime(DateInterval $interval)
    {
        $totalSeconds = $interval->h * 3600 
           + $interval->i * 60 + $interval->s;
        $hours = (int) floor($totalSeconds / 3600);
        $minutes = (int) floor(($totalSeconds - ($hours * 3600)) / 60);
        $seconds = $totalSeconds % 60;

        $result = '';
        if ($hours === 1)
            $result .= '1 hour';
        elseif ($hours > 1)
            $result .= $hours . ' hours';
        
        if ($hours && $minutes)
            $result .= ', ';

        if ($minutes === 1)
            $result .= '1 min';
        elseif ($minutes > 1)
            $result .= $minutes . ' mins';

        if (($hours || $minutes) && $seconds)
            $result .= ', ';

        if ($seconds === 1)
            $result .= '1 sec';
        elseif ($seconds > 1)
            $result .= $seconds . ' secs';

        return $result;
    }

}
