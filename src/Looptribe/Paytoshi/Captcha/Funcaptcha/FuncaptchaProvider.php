<?php

namespace Looptribe\Paytoshi\Captcha\Funcaptcha;

use Buzz\Browser;
use Buzz\Message\Response;
use Looptribe\Paytoshi\Captcha\CaptchaProviderException;
use Looptribe\Paytoshi\Captcha\CaptchaProviderInterface;
use Looptribe\Paytoshi\Captcha\CaptchaProviderResponse;

class FuncaptchaProvider implements CaptchaProviderInterface
{
    const VERIFY_URL = 'https://funcaptcha.com/fc/v/';

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

    function checkAnswer(array $options)
    {
        if (!isset($options['response']) || empty($options['response'])) {
            throw new CaptchaProviderException('Missing captcha response');
        }

        $headers = array(
            // Fix for Buzz HTTP 1.1
            'Connection' => 'Close',
        );

        $data = http_build_query(
            array(
                'private_key' => $this->privateKey,
                'session_token' => $options['response'],
                'simple_mode' => 1,
            )
        );

        $this->browser->getClient()->setVerifyPeer(false);

        try {
            /** @var Response $response */
            $response = $this->browser->post(self::VERIFY_URL, $headers, $data);
        } catch (\Exception $e) {
            throw new CaptchaProviderException(
                sprintf('Failed to send captcha: %s', $e->getMessage()), null, $e
            );
        }

        if (!$response->isSuccessful()) {
            throw new CaptchaProviderException('Captcha response error: '.$response->getStatusCode());
        }

        $content = $response->getContent();
        if (!$content) {
            throw new CaptchaProviderException('Invalid captcha response error');
        }

        $success = filter_var($content, FILTER_VALIDATE_BOOLEAN);

        return new CaptchaProviderResponse($success, $success ? null : 'Invalid Captcha');
    }
}