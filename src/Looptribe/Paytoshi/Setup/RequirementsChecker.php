<?php

namespace Looptribe\Paytoshi\Setup;

require_once dirname(__FILE__).'/../../../Requirements.php';

class RequirementsChecker
{
    /** @var \RequirementCollection */
    private $requirements;

    public function __construct()
    {
        $this->requirements = new \PaytoshiRequirements();
    }

    public function getRequirements()
    {
        return $this->requirements->getRequirements();
    }

    public function getRecommendations()
    {
        return $this->requirements->getRecommendations();
    }

    public function hasFailedRequirements()
    {
        return count($this->requirements->getFailedRequirements()) > 0;
    }
}
