<?php

namespace Looptribe\Paytoshi\Captcha;

class CaptchaProviderResponse
{
    /** @var  boolean */
    private $successful;
    /** @var string */
    private $message;

    public function __construct($successful, $message = null)
    {
        $this->successful = $successful;
        $this->message = $message;
    }

    public function isSuccessful()
    {
        return $this->successful;
    }

    public function getMessage()
    {
        return $this->message;
    }
}