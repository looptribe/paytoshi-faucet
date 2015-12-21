<?php

namespace Looptribe\Paytoshi\Model;

use Doctrine\DBAL\Connection;

class SetupDiagnostics
{
    /** @var SettingsRepository */
    private $settingsRepository;

    /** @var Connection */
    private $dabatase;

    /** @var string */
    private $configPath;

    public function __construct(Connection $dabatase, SettingsRepository $settingsRepository, $configPath)
    {
        $this->dabatase = $dabatase;
        $this->settingsRepository = $settingsRepository;
        $this->configPath = $configPath;
    }

    /**
     * @return bool
     */
    public function requiresSetup()
    {
        try {
            if (null === $this->settingsRepository->get('password')) {
                return true;
            }
        }
        catch (\Exception $ex) {
            return true;
        }
        return false;
    }

    public function isConfigWritable()
    {
        return is_writable($this->configPath);
    }
}
