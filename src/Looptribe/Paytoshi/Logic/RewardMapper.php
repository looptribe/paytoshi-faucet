<?php

namespace Looptribe\Paytoshi\Logic;


class RewardMapper
{
    /**
     * @param $rewardString
     * @return array
     */
    public function stringToArray($rewardString)
    {
        if (empty($rewardString))
            return array();

        $rewards = explode(',', $rewardString);
        $sortedRewards = array();
        foreach ($rewards as $reward) {
            $data = explode('*', $reward);
            if (count($data) != 2)
                continue;

            $sortedRewards[] = array(
                'amount' => intval($data[0]),
                'probability' => isset($data[1]) ? round(floatval($data[1]), 2) : 1
            );
        }
        usort($sortedRewards, function ($a, $b) {
            return $a['amount'] < $b['amount'] ? -1 : 1;
        });
        return $sortedRewards;
    }

    public function arrayToString(array $rewards)
    {
        if (empty($rewards))
            return '';

        //Unpack amount-probability couples
        $rewardArray = array_map(function ($i) {
            if (!isset($i['amount']) || !isset($i['probability'])) {
                return '';
            }
            return sprintf("%s*%s", $i['amount'], $i['probability']);
        }, $rewards);

        //Remove empty values
        $rewardArray = array_filter($rewardArray);

        return implode(',', $rewardArray);
    }
}