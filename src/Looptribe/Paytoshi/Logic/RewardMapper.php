<?php

namespace Looptribe\Paytoshi\Logic;


class RewardMapper
{
    public function stringToArray($rewardString)
    {
        if (empty($rewardString))
            return array();

        $rewards = explode(',', $rewardString);
        $sortedRewards = array();
        foreach ($rewards as $reward) {
            $data = explode('*', $reward);
            $sortedRewards[] = array(
                'amount' => intval($data[0]),
                'probability' => isset($data[1]) ? round(floatval($data[1]), 2) : 1
            );
            usort($sortedRewards, function ($a, $b) {
                return $a['amount'] < $b['amount'] ? -1 : 1;
            });
        }
        return $sortedRewards;
    }
}