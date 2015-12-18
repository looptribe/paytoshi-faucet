<?php

namespace Looptribe\Paytoshi\Logic;


class RewardProvider implements RewardProviderInterface
{
    /** @var array */
    private $rewards;

    public function __construct(array $rewards = array())
    {
        $this->rewards = $rewards;
    }

    public function getReward()
    {
        $total = $this->getTotalProbability();

        if ($total <= 0)
            return 0;

        $random = mt_rand(0, $total * 100);
        $random /= 100;

        $cumulative = 0;
        $reward = 0;
        foreach ($this->rewards as $r) {
            if (!$this->isValid($r))
                continue;

            $cumulative += $r['probability'];
            if ($random <= $cumulative) {
                $reward = $r['amount'];
                break;
            }
        }

        return $reward;
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

        $self = $this;
        $average = array_reduce($this->rewards, function($avg, $i) use($self) {
            return $self->isValid($i) ?
                $avg += $i['amount'] * $i['probability'] :
                $avg;
        }, 0);

        return round($average / $totalProbability, 2);
    }

    public function getMax()
    {
        $self = $this;
        return array_reduce($this->rewards, function($max, $i) use ($self) {
            return $self->isValid($i) ?
                $max = max($max, $i['amount']) :
                    $max;
        }, 0);
    }

    private function getTotalProbability()
    {
        $self = $this;
        return array_reduce($this->rewards, function ($sum, $i) use ($self) {
            return $self->isValid($i) ?
                $sum += $i['probability'] :
                $sum;
        }, 0);
    }

    public function getNormalized()
    {
        $total = $this->getTotalProbability();
        if ($total <= 0) {
            return array();
        }

        $rewards = array();

        foreach ($this->rewards as $reward) {
            if (!$this->isValid($reward))
                continue;

            $rewards[] = array(
                'amount' => $reward['amount'],
                'probability' => round(100 * $reward['probability'] / $total, 2)
            );
        }

        return $rewards;
    }

    private function isValid($reward)
    {
        return $reward &&
            isset($reward['probability']) && $reward['probability'] >= 0 &&
            isset($reward['amount']) && $reward['amount'] > 0;
    }
}