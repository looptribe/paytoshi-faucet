<?php

namespace Looptribe\Paytoshi\Model;

use DateTime;

class Recipient
{
    /** @var  integer */
    private $id;
    /** @var  string */
    private $address;
    /** @var  integer */
    private $earning;
    /** @var  integer */
    private $referralEarning;
    /** @var  DateTime */
    private $updatedAt;
    /** @var  DateTime */
    private $createdAt;

    public function __construct()
    {
        $this->earning = 0;
        $this->referralEarning = 0;
        $this->createdAt = new DateTime();
        $this->updatedAt = new DateTime();
    }

    /**
     * Set id
     *
     * @param string $id
     * @return Recipient
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
     * Set address
     *
     * @param string $address
     * @return Recipient
     */
    public function setAddress($address)
    {
        $this->address = $address;

        return $this;
    }

    /**
     * Get address
     *
     * @return string
     */
    public function getAddress()
    {
        return $this->address;
    }

    /**
     * Set earning
     *
     * @param integer $earning
     * @return Recipient
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
     * @return Recipient
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
     * @return Recipient
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

    /**
     * Set updatedAt
     *
     * @param \DateTime $updatedAt
     * @return Recipient
     */
    public function setUpdatedAt($updatedAt)
    {
        $this->updatedAt = $updatedAt;

        return $this;
    }

    /**
     * Get updatedAt
     *
     * @return \DateTime
     */
    public function getUpdatedAt()
    {
        return $this->updatedAt;
    }

    /**
     * @return bool
     */
    public function isNew()
    {
        return !$this->id;
    }

}