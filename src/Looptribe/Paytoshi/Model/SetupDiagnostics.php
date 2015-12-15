<?php

namespace Looptribe\Paytoshi\Model;

use Doctrine\DBAL\Connection;

class SetupDiagnostics
{
    /**
     * @var SettingsRepository
     */
    private $settingsRepository;

    /**
     * @var Connection
     */
    private $dabatase;

    public function __construct(Connection $dabatase, SettingsRepository $settingsRepository)
    {
        $this->dabatase = $dabatase;
        $this->settingsRepository = $settingsRepository;
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

    public function checkDatabase()
    {
        $this->dabatase->fetchAll('SHOW TABLES');
    }
}
