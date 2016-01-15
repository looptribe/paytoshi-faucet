<?php

namespace Looptribe\Paytoshi\Logic;

class RewardLogicResult
{
    const SEVERITY_SUCCESS = 'success';
    const SEVERITY_WARNING = 'warning';
    const SEVERITY_DANGER = 'danger';

    /** @var boolean */
    private $successful;
    /** @var string */
    private $severity;
    /** @var string */
    private $message;

    public function __construct($successful, $severity, $message = null)
    {
        $this->successful = $successful;
        $this->severity = $severity;
        $this->message = $message;
    }

    /**
     * @return boolean
     */
    public function isSuccessful()
    {
        return $this->successful;
    }

    /**
     * @return string
     */
    public function getSeverity()
    {
        return $this->severity;
    }

    /**
     * @return string
     */
    public function getMessage()
    {
        return $this->message;
    }


}