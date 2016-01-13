<?php

namespace Looptribe\Paytoshi\Tests\Logic;

use Looptribe\Paytoshi\Logic\RewardMapper;

class RewardMapperTest extends \PHPUnit_Framework_TestCase
{
    public function testStringToArray1()
    {
        $rewardString = '10*5,20*5';
        $sut = new RewardMapper();
        $results = $sut->stringToArray($rewardString);
        $this->assertEquals(
            array(
                array('probability' => 5, 'amount' => 10),
                array('probability' => 5, 'amount' => 20),
            ),
            $results
        );
    }

    public function testStringToArray2()
    {
        $rewardString = '20*5,10*5';
        $sut = new RewardMapper();
        $results = $sut->stringToArray($rewardString);
        $this->assertEquals(
            array(
                array('probability' => 5, 'amount' => 10),
                array('probability' => 5, 'amount' => 20),
            ),
            $results
        );
    }

    public function testStringToArray3()
    {
        $rewardString = '';
        $sut = new RewardMapper();
        $results = $sut->stringToArray($rewardString);
        $this->assertEquals(
            array(
            ),
            $results
        );
    }

    public function testStringToArray4()
    {
        $rewardString = '10*5';
        $sut = new RewardMapper();
        $results = $sut->stringToArray($rewardString);
        $this->assertEquals(
            array(
                array('probability' => 5, 'amount' => 10),
            ),
            $results
        );
    }

    public function testStringToArray5()
    {
        $rewardString = null;
        $sut = new RewardMapper();
        $results = $sut->stringToArray($rewardString);
        $this->assertEquals(
            array(
            ),
            $results
        );
    }

    public function testArrayToString1()
    {
        $rewards = array(
            array('probability' => 5, 'amount' => 10),
            array('probability' => 5, 'amount' => 20),
        );
        $sut = new RewardMapper();
        $results = $sut->arrayToString($rewards);
        $this->assertEquals('10*5,20*5', $results);
    }

    public function testArrayToString3()
    {
        $rewards = array(
        );
        $sut = new RewardMapper();
        $results = $sut->arrayToString($rewards);
        $this->assertEquals('', $results);
    }

    public function testArrayToString4()
    {
        $rewards = array(
            array('probability' => 5, 'amount' => 10),
        );
        $sut = new RewardMapper();
        $results = $sut->arrayToString($rewards);
        $this->assertEquals('10*5', $results);
    }
}