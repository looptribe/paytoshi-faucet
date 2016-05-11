<?php

namespace Looptribe\Paytoshi\Tests\Setup;

use Looptribe\Paytoshi\Setup\RequirementsChecker;

class RequirementsCheckerTest extends \PHPUnit_Framework_TestCase
{
    public function testGetRequirements()
    {
        $sut = new RequirementsChecker();
        $result = $sut->getRequirements();
        $this->assertCount(18, $result);
    }

    public function testGetRecommendations()
    {
        $sut = new RequirementsChecker();
        $result = $sut->getRecommendations();
        $this->assertCount(5, $result);
    }

    public function testHasFailedRequirements()
    {
        $sut = new RequirementsChecker();
        $result = $sut->hasFailedRequirements();
        $this->assertFalse($result);
    }
}
