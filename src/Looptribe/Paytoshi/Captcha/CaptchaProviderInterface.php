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
}