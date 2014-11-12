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
use Looptribe\Paytoshi\Exception\PaytoshiException;
use Looptribe\Paytoshi\Model\Payout;
use Looptribe\Paytoshi\Model\Recipient;
use Looptribe\Paytoshi\Service\DatabaseService;
use Looptribe\Paytoshi\Service\FaucetService;

class DefaultController {

    protected $app;
    /* @var $database DatabaseService */
    protected $database;
    /* @var $faucet FaucetService */
    protected $faucet;
    protected $captchaService;
    protected $recipientRepository;
    protected $payoutRepository;
    protected $apiService;
    protected $rewardService;

    public function __construct($app, $database, $faucet, $captchaServiceFactory, $recipientRepository, $payoutRepository, $apiService, $rewardService){
        $this->app = $app;
        $this->database = $database;
        $this->faucet = $faucet;
        $this->captchaService = $captchaServiceFactory->getService($this->faucet->getCaptchaProvider());
        $this->recipientRepository = $recipientRepository;
        $this->payoutRepository = $payoutRepository;
        $this->apiService = $apiService;
        $this->rewardService = $rewardService;
    }
    
    public function incomplete() {
        return $this->app->render('Default/incomplete.html.twig', array(
            'name' => $this->faucet->getName()
        ));
    }

    public function home() {
        
        return $this->app->render('Default/home.html.twig', array(
            'name' => $this->faucet->getName(),
            'description' => $this->faucet->getDescription(),
            'referral' => $this->app->request->get('r'),
            'referral_percentage' => $this->faucet->getReferralPercentage(),
            'rewards' => $this->rewardService->getAsArray(),
            'rewards_average' => $this->rewardService->getAverage(),
            'waiting_interval' => $this->faucet->getWaitingInterval(),
            'address' => $this->app->getCookie('address'),
            'base_url' => $this->app->request->getUrl(),
            'captcha' => array(
                'name' => $this->captchaService->getName(),
                'server' => $this->captchaService->getServer(),
                'public_key' => $this->captchaService->getPublicKey()
            ),
            'template' => array(
                'name' => $this->faucet->getTheme(),
                'css' => $this->faucet->getCss(),
                'header_box' => $this->faucet->getHeaderBox(),
                'left_box' => $this->faucet->getLeftBox(),
                'right_box' => $this->faucet->getRightBox(),
                'footer_box' => $this->faucet->getFooterBox(),
                'center1_box' => $this->faucet->getCenter1Box(),
                'center2_box' => $this->faucet->getCenter2Box(),
                'center3_box' => $this->faucet->getCenter3Box()
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
            $this->app->redirect($this->app->urlFor('home'));
        }
        
        $remoteIp = $this->app->request->getIp();

        // Captcha Check
        try {
            $captchaResponse = $this->captchaService->checkAnswer($remoteIp, $challenge, $response);
        }
        catch (CaptchaException $e) {
            $this->app->flash('error', 'Unable to complete request.');
            return $this->app->redirect($this->app->urlFor('home'));
        }
        
        if (!$captchaResponse->getSuccess()) {
            $this->app->flash('error', 'Invalid Captcha');
            return $this->app->redirect($this->app->urlFor('home'));
        }
        
        try {
            if (!$this->database->beginTransaction());
        }
        catch(PaytoshiException $e) {
            $this->app->flash('error', 'Unable to complete request.');
            $this->app->redirect($this->app->urlFor('home'));
        }
        
        $recipient = $this->recipientRepository->findOneByAddress($address);
        if (!$recipient) {
            $recipient = new Recipient();
            $recipient->setAddress($address);
        }
        
        // Timeout check
        $lastPayout = $this->payoutRepository->findLastByRecipientAndIp($recipient, $remoteIp);
        $now = new DateTime;
        $waitingInterval = $this->faucet->getWaitingInterval();
        if ($lastPayout) {
            $nextPayoutTime = $lastPayout->getCreatedAt()->add(new DateInterval('PT' . $waitingInterval . 'S'));
            if ($nextPayoutTime > $now)
            {
                $this->database->rollBack();
                $waitingTime = $nextPayoutTime->diff($now);
                $this->app->flash('warning', sprintf('You can get a reward again in %s.', $this->formatTime($waitingTime)));
                return $this->app->redirect($this->app->urlFor('home'));
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
            $apiResponse = $this->apiService->send($payout->getRecipientAddress(), $payout->getEarning());
        }
        catch(PaytoshiException $e) {
            $this->database->rollback();
            $this->app->flash('error', $e->getMessage());
            return $this->app->redirect($this->app->urlFor('home'));
        }
        
        if (!$apiResponse->getSuccess()) {
            $this->database->rollback();
            $this->app->flash('error', $apiResponse->getMessage());
            return $this->app->redirect($this->app->urlFor('home'));
        }
        
        $this->recipientRepository->save($recipient);
        $this->app->flash('success', $apiResponse->getMessage());
        $this->app->setCookie('address', $recipient->getAddress());
        
        $referral = $this->app->request->post('referral');
        $referralPercentage = $this->faucet->getReferralPercentage();
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
                return $this->app->redirect($this->app->urlFor('home'));
            }

            if ($apiResponse->getSuccess())
                $referralRecipient->setReferralEarning(0);
            
            $this->recipientRepository->save($referralRecipient);
        }
        $this->payoutRepository->save($payout);
        $this->database->commit();
        
        return $this->app->redirect($this->app->urlFor('home'));
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
