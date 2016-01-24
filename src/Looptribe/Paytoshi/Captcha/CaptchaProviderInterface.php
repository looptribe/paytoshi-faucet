<?php

namespace Looptribe\Paytoshi\Captcha;

interface CaptchaProviderInterface
{
    /**
     * @param array $options
     * @return CaptchaProviderResponse
     * @throws CaptchaProviderException
     */
    function checkAnswer(array $options);

    /**
     * @return string
     */
    function getChallengeName();

    /**
     * @return string
     */
    function getResponseName();

    /**
     * @return string
     */
    function getPublicKeyName();
}