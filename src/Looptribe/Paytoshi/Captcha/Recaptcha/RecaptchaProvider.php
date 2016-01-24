<?php

namespace Looptribe\Paytoshi\Captcha\Recaptcha;

use Buzz\Browser;
use Buzz\Message\Response;
use Looptribe\Paytoshi\Captcha\CaptchaProviderException;
use Looptribe\Paytoshi\Captcha\CaptchaProviderInterface;
use Looptribe\Paytoshi\Captcha\CaptchaProviderResponse;

class RecaptchaProvider implements CaptchaProviderInterface
{
    const VERIFY_URL = 'https://www.google.com/recaptcha/api/siteverify';

    /** @var string */
    private $publicKey;
    /** @var string */
    private $privateKey;
    /** @var Browser */
    private $browser;

    public function __construct(Browser $browser, $publicKey, $privateKey)
    {

        $this->publicKey = $publicKey;
        $this->privateKey = $privateKey;
        $this->browser = $browser;
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

        if (!isset($options['response']) || empty($options['response'])) {
            throw new CaptchaProviderException('Missing captcha response');
        }

        $headers = array(
            // Fix for Buzz HTTP 1.1
            'Connection' => 'Close',
        );

        $data = http_build_query(
            array(
                'secret' => $this->privateKey,
                'response' => $options['response'],
                'remoteip' => $options['ip'],
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

        $content = json_decode($response->getContent());
        if (!$content) {
            throw new CaptchaProviderException('Invalid captcha response error');
        }

        $success = isset($content->success) && filter_var($content->success, FILTER_VALIDATE_BOOLEAN);
        return new CaptchaProviderResponse($success, $success ? null : 'Invalid Captcha');
    }

    /**
     * @return string
     */
    function getChallengeName()
    {
        return '';
    }

    /**
     * @return string
     */
    function getResponseName()
    {
        return 'g-recaptcha-response';
    }
}