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

class RewardService {
    protected $rewards;
    
    public function __construct($rewards)
    {
        $this->rewards = $rewards;
    }
    
    public function getReward() {
        //2nd digit precision
        $random = mt_rand(1, $this->getTotalProbability() * 100);
        $random /= 100;
        
        $cumulative = 0;
        foreach ($this->rewards as $reward)
        {
            $cumulative += $reward['probability'];
            if ($random <= $cumulative)
                return $reward['amount'];
        }
    }
    
    public function getAsArray() {
        return $this->rewards;
    }
    
    public function getAverage() {
        $totalProbability = $this->getTotalProbability();
        if ($totalProbability <= 0)
            return 0;
        
        $average = 0;
        foreach($this->rewards as $reward)
            $average += $reward['amount'] * $reward['probability'];
        
        return round($average / $totalProbability, 2);
    }
    
    public function getTotalProbability() {
        $totalProbability = 0;
        foreach ($this->rewards as $reward)
            $totalProbability += $reward['probability'];
        return $totalProbability;
    }
}
