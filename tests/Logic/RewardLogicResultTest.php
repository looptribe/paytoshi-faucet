<?php

namespace Looptribe\Paytoshi\Tests\Logic;

use Looptribe\Paytoshi\Logic\RewardLogicResult;

class RewardLogicResultTest extends \PHPUnit_Framework_TestCase
{
    public function testConstructor1()
    {
        $sut = new RewardLogicResult(true, RewardLogicResult::SEVERITY_SUCCESS);
        $this->assertSame(true, $sut->isSuccessful());
        $this->assertSame('success', $sut->getSeverity());
    }

    public function testConstructor2()
    {
        $sut = new RewardLogicResult(false, RewardLogicResult::SEVERITY_WARNING);
        $this->assertSame(false, $sut->isSuccessful());
        $this->assertSame('warning', $sut->getSeverity());
    }

    public function testConstructor3()
    {
        $sut = new RewardLogicResult(false, RewardLogicResult::SEVERITY_DANGER, 'Error');
        $this->assertSame(false, $sut->isSuccessful());
        $this->assertSame('danger', $sut->getSeverity());
        $this->assertSame('Error', $sut->getMessage());
    }
}
