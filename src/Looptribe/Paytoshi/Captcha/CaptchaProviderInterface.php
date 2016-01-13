<?php

namespace Looptribe\Paytoshi\Captcha;

interface CaptchaProviderInterface
{
    /**
     * @param array $options
     * @return CaptchaProviderInterface
     * @throws CaptchaProviderException
     */
    function checkAnswer(array $options);
}