<?php

namespace Looptribe\Paytoshi\Logic;

use Looptribe\Paytoshi\Api\Response\FaucetSendResponse;

class RewardLogicResult
{
    const SEVERITY_WARNING = 'warning';
    const SEVERITY_DANGER = 'danger';

    /** @var boolean */
    private $successful;
    /** @var string */
    private $severity;
    /** @var string */
    private $error;
    /** @var FaucetSendResponse */
    private $response;

    public function __construct()
    {
        $this->successful = false;
    }

    /**
     * @return boolean
     */
    public function isSuccessful()
    {
        return $this->successful;
    }

    /**
     * @param boolean $successful
     * @return RewardLogicResult
     */
    public function setSuccessful($successful)
    {
        $this->successful = $successful;

        return $this;
    }

    /**
     * @return string
     */
    public function getSeverity()
    {
        return $this->severity;
    }

    /**
     * @param string $severity
     * @return RewardLogicResult
     */
    public function setSeverity($severity)
    {
        $this->severity = $severity;

        return $this;
    }

    /**
     * @return string
     */
    public function getError()
    {
        return $this->error;
    }

    /**
     * @param string $error
     * @return RewardLogicResult
     */
    public function setError($error)
    {
        $this->error = $error;

        return $this;
    }

    /**
     * @return FaucetSendResponse
     */
    public function getResponse()
    {
        return $this->response;
    }

    /**
     * @param FaucetSendResponse $response
     * @return RewardLogicResult
     */
    public function setResponse($response)
    {
        $this->response = $response;

        return $this;
    }

}