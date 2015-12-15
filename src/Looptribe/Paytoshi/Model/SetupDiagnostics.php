<?php

namespace Looptribe\Paytoshi\Model;

class SetupDiagnostics
{
    /**
     * @var SettingsRepository
     */
    private $settingsRepository;

    public function __construct(SettingsRepository $settingsRepository)
    {
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
}
