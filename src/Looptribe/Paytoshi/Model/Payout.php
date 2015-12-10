<?php

/**
 * Paytoshi Faucet Script
 *
 * Contact: info@paytoshi.org
 *
 * @author: Looptribe
 * @link: https://paytoshi.org
 * @package: Looptribe\Paytoshi
 */

namespace Looptribe\Paytoshi\Model;

use DateTime;

class Payout
{
    private $id;
    private $earning;
    private $referralEarning;
    private $ip;
    private $recipientAddress;
    private $referralRecipientAddress;
    private $createdAt;

    public function __construct()
    {
        $this->earning = 0;
        $this->referralEarning = 0;
        $this->createdAt = new DateTime();
    }

    /**
     * Set id
     *
     * @param string $id
     * @return Payout
     */
    public function setId($id)
    {
        $this->id = $id;
        return $this;
    }

    /**
     * Get id
     *
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set ip
     *
     * @param string $ip
     * @return Payout
     */
    public function setIp($ip)
    {
        $this->ip = $ip;
        return $this;
    }

    /**
     * Get ip
     *
     * @return Recipient
     */
    public function getIp()
    {
        return $this->ip;
    }

    /**
     * Set recipientAddress
     *
     * @param string $recipientAddress
     * @return Payout
     */
    public function setRecipientAddress($recipientAddress)
    {
        $this->recipientAddress = $recipientAddress;
        return $this;
    }

    /**
     * Get recipientAddress
     *
     * @return string
     */
    public function getRecipientAddress()
    {
        return $this->recipientAddress;
    }

    /**
     * Set referralRecipientAddress
     *
     * @param Recipient $referralRecipientAddress
     * @return Recipient
     */
    public function setReferralRecipientAddress($referralRecipientAddress)
    {
        $this->referralRecipientAddress = $referralRecipientAddress;
        return $this;
    }

    /**
     * Get referralRecipientAddress
     *
     * @return Recipient
     */
    public function getReferralRecipientAddress()
    {
        return $this->referralRecipientAddress;
    }

    /**
     * Set earning
     *
     * @param integer $earning
     * @return Payout
     */
    public function setEarning($earning)
    {
        $this->earning = $earning;
        return $this;
    }

    /**
     * Get earning
     *
     * @return integer
     */
    public function getEarning()
    {
        return $this->earning;
    }

    /**
     * Set referralEarning
     *
     * @param integer $referralEarning
     * @return Payout
     */
    public function setReferralEarning($referralEarning)
    {
        $this->referralEarning = $referralEarning;
        return $this;
    }

    /**
     * Get referralEarning
     *
     * @return integer
     */
    public function getReferralEarning()
    {
        return $this->referralEarning;
    }

    /**
     * Set createdAt
     *
     * @param \DateTime $createdAt
     * @return Payout
     */
    public function setCreatedAt($createdAt)
    {
        $this->createdAt = $createdAt;
        return $this;
    }

    /**
     * Get createdAt
     *
     * @return \DateTime
     */
    public function getCreatedAt()
    {
        return $this->createdAt;
    }

}
