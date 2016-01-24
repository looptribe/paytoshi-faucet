<?php

namespace Looptribe\Paytoshi\Captcha\SolveMedia;

use Buzz\Browser;
use Buzz\Message\Response;
use Looptribe\Paytoshi\Captcha\CaptchaProviderException;
use Looptribe\Paytoshi\Captcha\CaptchaProviderInterface;
use Looptribe\Paytoshi\Captcha\CaptchaProviderResponse;

class SolveMediaProvider implements CaptchaProviderInterface
{
    const VERIFY_URL = 'http://verify.solvemedia.com/papi/verify';

    /** @var string */
    private $publicKey;
    /** @var string */
    private $privateKey;
    /** @var string */
    private $verificationKey;
    /** @var Browser */
    private $browser;

    public function __construct(Browser $browser, $publicKey, $privateKey, $verificationKey)
    {

        $this->publicKey = $publicKey;
        $this->privateKey = $privateKey;
        $this->browser = $browser;
        $this->verificationKey = $verificationKey;
    }

    /**
     * @param array $options
     * @return CaptchaProviderResponse
     * @throws CaptchaProviderException
     */
    function checkAnswer(array $options)
    {
        if (!isset($options['ip']) || empty($options['ip'])) {
            throw new CaptchaProviderException('Missing captcha ip');
        }

        if (!isset($options['challenge']) || empty($options['challenge'])) {
            throw new CaptchaProviderException('Missing captcha challenge');
        }

        if (!isset($options['response']) || empty($options['response'])) {
            throw new CaptchaProviderException('Missing captcha response');
        }

        $headers = array(
            'User-Agent' => 'solvemedia/PHP',
            'Content-Type' => 'application/x-www-form-urlencoded',
            // Fix for Buzz HTTP 1.1
            'Connection' => 'Close',
        );

        $data = http_build_query(
            array(
                'privatekey' => $this->privateKey,
                'remoteip' => $options['ip'],
                'challenge' => $options['challenge'],
                'response' => $options['response']
            )
        );

        try {
            /** @var Response $response */
            $response = $this->browser->post(self::VERIFY_URL, $headers, $data);
        } catch (\Exception $e) {
            throw new CaptchaProviderException(
                sprintf('Failed to send captcha: %s', $e->getMessage()), null, $e
            );
        }

        if (!$response->isSuccessful()) {
            throw new CaptchaProviderException('Captcha response error: ' . $response->getStatusCode());
        }

        $content = $response->getContent();
        if (!$content) {
            throw new CaptchaProviderException('Invalid captcha response error');
        }
        /**
         * 0: true|false
         * 1: errorMessage (optional)
         * 2: hash
         */
        $content = explode("\n", $content);

        $success = isset($content[0]) && filter_var($content[0], FILTER_VALIDATE_BOOLEAN);
        if (!$success) {
            return new CaptchaProviderResponse(false, 'Invalid Captcha');
        }

        $hashMaterial = $content[0] . $options['challenge'] . $this->verificationKey;
        $hash = sha1($hashMaterial);
        if (isset($content[2]) && $hash != $content[2]) {
            return new CaptchaProviderResponse(false, 'Invalid Captcha verification');
        }

        return new CaptchaProviderResponse(true);
    }

    /**
     * @return string
     */
    function getChallengeName()
    {
        return 'adcopy_challenge';
    }

    /**
     * @return string
     */
    function getResponseName()
    {
        return 'adcopy_response';
    }

    /**
     * @return string
     */
    function getPublicKeyName()
    {
        return 'solve_media_challenge_key';
    }
}