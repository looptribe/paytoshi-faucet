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

namespace Looptribe\Paytoshi\Service;

class RewardService
{
    protected $rewards;

    public function __construct($rewards)
    {
        $this->rewards = $rewards;
    }

    public function getReward()
    {
        //2nd digit precision
        $random = mt_rand(1, $this->getTotalProbability() * 100);
        $random /= 100;

        $cumulative = 0;
        foreach ($this->rewards as $reward) {
            $cumulative += $reward['probability'];
            if ($random <= $cumulative) {
                return $reward['amount'];
            }
        }
    }

    public function getAsArray()
    {
        return $this->rewards;
    }

    public function getAverage()
    {
        $totalProbability = $this->getTotalProbability();
        if ($totalProbability <= 0) {
            return 0;
        }

        $average = 0;
        foreach ($this->rewards as $reward) {
            $average += $reward['amount'] * $reward['probability'];
        }

        return round($average / $totalProbability, 2);
    }

    public function getMax()
    {
        $max = 0;
        foreach ($this->rewards as $reward) {
            if ($reward['probability']) {
                $max = max($max, $reward['amount']);
            }
        }
        return $max;
    }

    public function getTotalProbability()
    {
        $totalProbability = 0;
        foreach ($this->rewards as $reward) {
            $totalProbability += $reward['probability'];
        }
        return $totalProbability;
    }

    public function getNormalized()
    {
        $total = $this->getTotalProbability();
        if ($total <= 0) {
            return;
        }

        $rewards = array();

        foreach ($this->rewards as $reward) {
            $rewards[] = array(
                'amount' => $reward['amount'],
                'probability' => round(100 * $reward['probability'] / $total, 2)
            );
        }

        return $rewards;
    }
}
