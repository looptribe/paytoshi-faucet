<?php
namespace Looptribe\Paytoshi\Logic;

interface RewardProviderInterface
{
    public function getReward();

    public function getAsArray();

    public function getAverage();

    public function getMax();

    public function getNormalized();
}