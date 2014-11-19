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

namespace Looptribe\Paytoshi\Model;

use DateTime;

class SettingRepository {
    
    const TABLE_NAME = 'paytoshi_settings';
    
    protected $database;
    protected $data = array();

    public function __construct($database) {
        $this->database = $database;
        $sql = sprintf("SELECT * FROM %s", self::TABLE_NAME);
        $results = $this->database->run($sql);
        foreach ($results as $row) {
            $this->data[$row['name']] = $row['value'];
        }
    }
    
    public function isNew() {
        return !isset($this->data['password']) || !$this->data['password'];
    }
    
    public function isIncomplete() {
        return !$this->data['installed_at'] || !$this->data['api_key'];
    }
    
    public function getName() {
        return $this->data['name'];
    }
    
    public function getDescription() {
        return $this->data['description'];
    }
    
    public function getReferralPercentage() {
        return intval($this->data['referral_percentage']);
    }
    
    public function getPassword() {
        return isset($this->data['password']) ? $this->data['password'] : '';
    }
    
    public function getCookieSecretKey() {
        return isset($this->data['cookie_secret_key']) ? $this->data['cookie_secret_key'] : '';
    }
    
    public function getSolveMediaChallengeKey() {
        return $this->data['solve_media_challenge_key'];
    }
    
    public function getSolveMediaVerificationKey() {
        return $this->data['solve_media_verification_key'];
    }
    
    public function getSolveMediaAuthenticationKey() {
        return $this->data['solve_media_authentication_key'];
    }
    
    public function getApiKey() {
        return $this->data['api_key'];
    }
    
    public function getTheme() {
        return strtolower($this->data['theme']);
    }
    
    public function getCss() {
        return $this->data['custom_css'];
    }
    
    public function getHeaderBox() {
        return $this->data['content_header_box'];
    }
    
    public function getLeftBox() {
        return $this->data['content_left_box'];
    }
    
    public function getRightBox() {
        return $this->data['content_right_box'];
    }
    
    public function getCenter1Box() {
        return $this->data['content_center1_box'];
    }
    
    public function getCenter2Box() {
        return $this->data['content_center2_box'];
    }
    
    public function getCenter3Box() {
        return $this->data['content_center3_box'];
    }

    public function getFooterBox() {
        return $this->data['content_footer_box'];
    }
    
    public function getVersion() {
        return $this->data['version'];
    }
    
    public function getAdminView() {
        return array(
            'version' => $this->getVersion(),
            'api_key' => $this->getApiKey(),
            'name' => $this->getName(),
            'description' => $this->getDescription(),
            'current_theme' => $this->getTheme(),
            'captcha_provider' => $this->getCaptchaProvider(),
            'solve_media' => array(
                'challenge_key' => $this->getSolveMediaChallengeKey(),
                'verification_key' => $this->getSolveMediaVerificationKey(),
                'authentication_key' => $this->getSolveMediaAuthenticationKey(),
            ),
            'waiting_interval' => $this->getWaitingInterval(),
            'rewards' => $this->getRewards(),
            'referral_percentage' => $this->getReferralPercentage(),
            'css' => $this->getCss(),
            'header_box' => $this->getHeaderBox(),
            'left_box' => $this->getLeftBox(),
            'right_box' => $this->getRightBox(),
            'center1_box' => $this->getCenter1Box(),
            'center2_box' => $this->getCenter2Box(),
            'center3_box' => $this->getCenter3Box(),
            'footer_box' => $this->getFooterBox(),
        );
        
    }
    
    public function getCaptchaProvider() {
        return $this->data['captcha_provider'];
    }
    
    public function getRewards() {
        return $this->rewardsCleanup($this->data['rewards']);
    }
    
    public function getWaitingInterval() {
        return intval($this->data['waiting_interval']);
    }
    
    public function getInstalledAt() {
        return $this->data['installed_at'];
    }
    
    public function save($data) {
        $rewards = implode(',',array_map(function($i) { 
            if (!isset($i['amount']) || !isset($i['probability']))
                return '';
            return sprintf("%s*%s", $i['amount'], $i['probability']); 
        }, $data['rewards']));
        
        // TODO: theme/captcha support
        $fields = array(
            'api_key' => ':api_key',
            'name' => ':name',
            'description' => ':description',
            'waiting_interval' => ':waiting_interval',
            'rewards' => ':rewards',
            'referral_percentage' => ':referral_percentage',
            'custom_css' => ':css',
            'content_header_box' => ':header_box',
            'content_left_box' => ':left_box',
            'content_right_box' => ':right_box',
            'content_footer_box' => ':footer_box',
            'content_center1_box' => ':center1_box',
            'content_center2_box' => ':center2_box',
            'content_center3_box' => ':center3_box',
            'theme' => ':theme'
        );
        
        $params = array(
            ':api_key' => trim($data['api_key']),
            ':name' => trim($data['name']),
            ':description' => trim($data['description']),
            ':waiting_interval' => intval(trim($data['waiting_interval'])),
            ':rewards' => trim($rewards),
            ':referral_percentage' => intval(trim($data['referral_percentage'])),
            ':css' => trim($data['css']),
            ':header_box' => trim($data['header_box']),
            ':left_box' => trim($data['left_box']),
            ':right_box' => trim($data['right_box']),
            ':footer_box' => trim($data['footer_box']),
            ':center1_box' => trim($data['center1_box']),
            ':center2_box' => trim($data['center2_box']),
            ':center3_box' => trim($data['center3_box']),
            ':theme' => trim($data['theme'])
        );
        
        if ($this->getCaptchaProvider() == 'solve_media')
            $fields = array_merge($fields, array(
                'solve_media_challenge_key' => ':solve_media_challenge_key',
                'solve_media_verification_key' => ':solve_media_verification_key',
                'solve_media_authentication_key' => ':solve_media_authentication_key'
            ));
            $params = array_merge($params, array(
                ':solve_media_challenge_key' => trim($data['solve_media']['challenge_key']),
                ':solve_media_verification_key' => trim($data['solve_media']['verification_key']),
                ':solve_media_authentication_key' => trim($data['solve_media']['authentication_key'])
            ));
                
        if (!$this->getInstalledAt()) {
            $fields = array_merge($fields, array(
                'installed_at' => ':installed_at'
            ));
            $now = new DateTime();
            $params = array_merge($params, array(
                ':installed_at' => $now->format('Y-m-d H:i:s')
            ));
        }
        
        $sql = sprintf("UPDATE %s SET `value` = CASE `name` ", self::TABLE_NAME);
        foreach ($fields as $k => $v)
            $sql .= sprintf ("WHEN '%s' THEN %s ", $k, $v);
        $sql .= 'END WHERE `name` IN (';
        $sql .= implode(', ', array_map(function($i) { return "'$i'"; }, array_keys($fields)));
        $sql .= ')';

        $this->database->run($sql, $params);
    }
    
    private function rewardsCleanup($rewards) {
        $rewards = explode(',', $rewards);
        $sortedRewards = array();
        foreach ($rewards as $reward) {
            $data = explode('*', $reward);
            $sortedRewards[] = array(
                'amount' => intval($data[0]),
                'probability' => isset($data[1]) ? round(floatval($data[1]), 2) : 1
            );
            usort($sortedRewards, function ($a, $b) {
                return $a['amount'] < $b['amount'] ? -1 : 1;
            });
        }
        return $sortedRewards;
    }
    
}
