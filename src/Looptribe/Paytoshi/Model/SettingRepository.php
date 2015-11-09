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
use Looptribe\Paytoshi\Service\DatabaseService;

class SettingRepository
{
    const TABLE_NAME = 'paytoshi_settings';

    /** @var DatabaseService */
    protected $database;

    protected $data = array();

    public function __construct(DatabaseService $database)
    {
        $this->database = $database;
        $sql = sprintf("SELECT * FROM %s", self::TABLE_NAME);
        $results = $this->database->run($sql);
        foreach ($results as $row) {
            $this->data[$row['name']] = $row['value'];
        }
    }

    public function isNew()
    {
        return !isset($this->data['password']) || !$this->data['password'];
    }

    public function isIncomplete()
    {
        return !$this->data['installed_at'] || !$this->data['api_key'];
    }

    public function getName()
    {
        return $this->get('name');
    }

    public function getDescription()
    {
        return $this->get('description');
    }

    public function getReferralPercentage()
    {
        return intval($this->get('referral_percentage', 0));
    }

    public function getPassword()
    {
        return $this->get('password');
    }

    public function getSolveMediaChallengeKey()
    {
        return $this->get('solve_media_challenge_key');
    }

    public function getSolveMediaVerificationKey()
    {
        return $this->get('solve_media_verification_key');
    }

    public function getSolveMediaAuthenticationKey()
    {
        return $this->get('solve_media_authentication_key');
    }

    public function getRecaptchaPublicKey()
    {
        return $this->get('recaptcha_public_key');
    }

    public function getRecaptchaPrivateKey()
    {
        return $this->get('recaptcha_private_key');
    }

    public function getApiKey()
    {
        return $this->get('api_key');
    }

    public function getTheme()
    {
        return strtolower($this->get('theme', 'default'));
    }

    public function getCss()
    {
        return $this->get('custom_css');
    }

    public function getHeaderBox()
    {
        return $this->get('content_header_box');
    }

    public function getLeftBox()
    {
        return $this->get('content_left_box');
    }

    public function getRightBox()
    {
        return $this->get('content_right_box');
    }

    public function getCenter1Box()
    {
        return $this->get('content_center1_box');
    }

    public function getCenter2Box()
    {
        return $this->get('content_center2_box');
    }

    public function getCenter3Box()
    {
        return $this->get('content_center3_box');
    }

    public function getFooterBox()
    {
        return $this->get('content_footer_box');
    }

    public function getVersion()
    {
        return $this->get('version');
    }

    public function getCaptchaProvider()
    {
        return $this->get('captcha_provider');
    }

    public function getRewards()
    {
        return $this->rewardsCleanup($this->get('rewards'));
    }

    public function getWaitingInterval()
    {
        return intval($this->get('waiting_interval'));
    }

    public function getInstalledAt()
    {
        return $this->get('installed_at');
    }

    public function save($data)
    {
        $rewards = implode(',', array_map(function ($i) {
            if (!isset($i['amount']) || !isset($i['probability'])) {
                return '';
            }
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
            'theme' => ':theme',
            'captcha_provider' => ':captcha_provider',
            'solve_media_challenge_key' => ':solve_media_challenge_key',
            'solve_media_verification_key' => ':solve_media_verification_key',
            'solve_media_authentication_key' => ':solve_media_authentication_key',
            'recaptcha_public_key' => ':recaptcha_public_key',
            'recaptcha_private_key' => ':recaptcha_private_key',
        );

        $this->insertMissingFields($fields);

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
            ':theme' => trim($data['theme']),
            ':captcha_provider' => trim($data['captcha_provider']),
            ':solve_media_challenge_key' => trim($data['solve_media']['challenge_key']),
            ':solve_media_verification_key' => trim($data['solve_media']['verification_key']),
            ':solve_media_authentication_key' => trim($data['solve_media']['authentication_key']),
            ':recaptcha_public_key' => trim($data['recaptcha']['public_key']),
            ':recaptcha_private_key' => trim($data['recaptcha']['private_key']),
        );

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
        foreach ($fields as $k => $v) {
            $sql .= sprintf("WHEN '%s' THEN %s ", $k, $v);
        }
        $sql .= 'END WHERE `name` IN (';
        $sql .= implode(', ', array_map(function ($i) {
            return "'$i'";
        }, array_keys($fields)));
        $sql .= ')';

        $this->database->run($sql, $params);
    }

    private function rewardsCleanup($rewards)
    {
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

    private function get($key, $default = null)
    {
        return isset($this->data[$key]) ? $this->data[$key] : $default;
    }

    private function insertMissingFields($fields)
    {
        $existing = array_keys($this->data);
        $required = array_keys($fields);
        $newFields = array_diff($required, $existing);
        if (count($newFields) > 0) {
            $sql = sprintf("INSERT INTO `%s` (`name`, `value`) VALUES ", self::TABLE_NAME);
            $sql .= implode(', ', array_map(function ($i) {
                return "('$i', '')";
            }, $newFields));
            $this->database->run($sql);
        }
    }
}
