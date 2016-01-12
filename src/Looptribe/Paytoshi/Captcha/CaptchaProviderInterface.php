<?php

namespace Looptribe\Paytoshi\Captcha;

interface CaptchaProviderInterface
{
    /**
     * @param array $options
     * @return mixed
     */
    function checkAnswer(array $options);
}