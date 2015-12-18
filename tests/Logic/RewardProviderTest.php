<?php

namespace Looptribe\Paytoshi\Tests\Logic;

use Looptribe\Paytoshi\Logic\RewardProvider;

class RewardProviderTest extends \PHPUnit_Framework_TestCase
{
    public function testGetAsArray1()
    {
        $rewards = array();
        $sut = new RewardProvider($rewards);
        $rewards = $sut->getAsArray();
        $this->assertEquals(array(), $rewards);
    }

    public function testGetAsArray2()
    {
        $rewards = array(
            array('probability' => 5, 'amount' => 10),
            array('probability' => 5, 'amount' => 20),
        );
        $sut = new RewardProvider($rewards);
        $rewardArray = $sut->getAsArray();
        $this->assertEquals($rewards, $rewardArray);
    }

    public function testGetReward1()
    {
        $rewards = array();
        $sut = new RewardProvider($rewards);
        $reward = $sut->getReward();
        $this->assertEquals(0, $reward);
    }

    public function testGetReward2()
    {
        $rewards = array(
            array('probability' => 0, 'amount' => 10)
        );
        $sut = new RewardProvider($rewards);
        $reward = $sut->getReward();
        $this->assertEquals(0, $reward);
    }

    public function testGetReward3()
    {
        $rewards = array(
            array('probability' => 5, 'amount' => 10)
        );
        $sut = new RewardProvider($rewards);
        $reward = $sut->getReward();
        $this->assertEquals(10, $reward);
    }

    public function testGetReward4()
    {
        $rewards = array(
            array('probability' => 5, 'amount' => 10),
            array('probability' => 15, 'amount' => 20),
            array('probability' => 5, 'amount' => 30),
        );
        $sut = new RewardProvider($rewards);
        $reward = $sut->getReward();
        $this->assertEquals(10 || 20 || 30, $reward);
    }

    public function testGetReward5()
    {
        $rewards = array(
            array('probability' => -5, 'amount' => 10),
        );
        $sut = new RewardProvider($rewards);
        $reward = $sut->getReward();
        $this->assertEquals(0, $reward);
    }

    public function testGetReward6()
    {
        $rewards = array(
            array('probability' => 5, 'amount' => -10),
        );
        $sut = new RewardProvider($rewards);
        $reward = $sut->getReward();
        $this->assertEquals(0, $reward);
    }

    public function testGetAverage1()
    {
        $rewards = array(
        );
        $sut = new RewardProvider($rewards);
        $avg = $sut->getAverage();
        $this->assertEquals(0, $avg);
    }

    public function testGetAverage2()
    {
        $rewards = array(
            array('probability' => 5, 'amount' => 10),
            array('probability' => 15, 'amount' => 20),
            array('probability' => 5, 'amount' => 30),
        );
        $sut = new RewardProvider($rewards);
        $avg = $sut->getAverage();
        $this->assertEquals(20, $avg);
    }

    public function testGetAverage3()
    {
        $rewards = array(
            array('probability' => 5, 'amount' => 10),
            array('probability' => -15, 'amount' => 20),
            array('probability' => 5, 'amount' => 30),
        );
        $sut = new RewardProvider($rewards);
        $avg = $sut->getAverage();
        $this->assertEquals(20, $avg);
    }

    public function testGetAverage4()
    {
        $rewards = array(
            array('probability' => 5, 'amount' => 10),
            array('probability' => 15, 'amount' => -20),
            array('probability' => 5, 'amount' => 30),
        );
        $sut = new RewardProvider($rewards);
        $avg = $sut->getAverage();
        $this->assertEquals(20, $avg);
    }

    public function testGetAverage5()
    {
        $rewards = array(
            array('probability' => 1.25, 'amount' => 1),
            array('probability' => 1, 'amount' => 2),
        );
        $sut = new RewardProvider($rewards);
        $avg = $sut->getAverage();
        $this->assertEquals(1.44, $avg);
    }


    public function testGetMax1()
    {
        $rewards = array(
        );
        $sut = new RewardProvider($rewards);
        $max = $sut->getMax();
        $this->assertEquals(0, $max);
    }

    public function testGetMax2()
    {
        $rewards = array(
            array('probability' => 5, 'amount' => 10),
            array('probability' => 15, 'amount' => 20),
            array('probability' => 5, 'amount' => 30),
        );
        $sut = new RewardProvider($rewards);
        $max = $sut->getMax();
        $this->assertEquals(30, $max);
    }

    public function testGetMax3()
    {
        $rewards = array(
            array('probability' => 5, 'amount' => 10),
            array('probability' => 15, 'amount' => 20),
            array('probability' => -5, 'amount' => 30),
        );
        $sut = new RewardProvider($rewards);
        $max = $sut->getMax();
        $this->assertEquals(20, $max);
    }

    public function testGetMax4()
    {
        $rewards = array(
            array('probability' => 5, 'amount' => 10),
            array('probability' => 15, 'amount' => 20),
            array('probability' => 5, 'amount' => -30),
        );
        $sut = new RewardProvider($rewards);
        $max = $sut->getMax();
        $this->assertEquals(20, $max);
    }

    public function testGetNormalized1()
    {
        $rewards = array(
        );
        $sut = new RewardProvider($rewards);
        $normalized = $sut->getNormalized();
        $this->assertEquals(array(), $normalized);
    }

    public function testGetNormalized2()
    {
        $rewards = array(
            array('probability' => 5, 'amount' => 10),
            array('probability' => 15, 'amount' => 20),
            array('probability' => 5, 'amount' => 30),
        );
        $sut = new RewardProvider($rewards);
        $normalized = $sut->getNormalized();
        $this->assertEquals(array(
            array('probability' => 20, 'amount' => 10),
            array('probability' => 60, 'amount' => 20),
            array('probability' => 20, 'amount' => 30),
        ), $normalized);
    }

    public function testGetNormalized3()
    {
        $rewards = array(
            array('probability' => 5, 'amount' => 10),
            array('probability' => -15, 'amount' => 20),
            array('probability' => 5, 'amount' => 30),
        );
        $sut = new RewardProvider($rewards);
        $normalized = $sut->getNormalized();
        $this->assertEquals(array(
            array('probability' => 50, 'amount' => 10),
            array('probability' => 50, 'amount' => 30),
        ), $normalized);
    }

    public function testGetNormalized4()
    {
        $rewards = array(
            array('probability' => 5, 'amount' => 10),
            array('probability' => 15, 'amount' => -20),
            array('probability' => 5, 'amount' => 30),
        );
        $sut = new RewardProvider($rewards);
        $normalized = $sut->getNormalized();
        $this->assertEquals(array(
            array('probability' => 50, 'amount' => 10),
            array('probability' => 50, 'amount' => 30),
        ), $normalized);
    }

}
