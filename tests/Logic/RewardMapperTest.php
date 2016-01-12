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
}